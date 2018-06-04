<?php

namespace se\eab\php\laravel\modelgenerator;

use se\eab\php\classtailor\model\ClassFile;
use se\eab\php\laravel\modelgenerator\provider\ModelGeneratorServiceProvider;
use se\eab\php\classtailor\ClassTailor;
use se\eab\php\classtailor\factory\ClassFileFactory;
use Artisan;
use se\eab\php\laravel\modelgenerator\config\ModelGeneratorConfigHelper;

/**
 * Description of ModelGenerator
 *
 * @author dsvenss
 */
class ModelGenerator
{

    private static $instance;
    private $classtailor;
    private $modelconfigbasedir;

    /**
     * @var ClassFile
     */
    private $commonclassfile;

    /**
     * @var ClassFile[]
     */
    private $qualifiedExtraClassfiles;

    private function __construct()
    {
        $this->classtailor = new ClassTailor();
        $this->modelconfigbasedir = ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . DIRECTORY_SEPARATOR;
        $this->initCommonClassFile();
        $this->initExtraModelAdjustments();
    }

    /**
     *
     * @return ModelGenerator
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new ModelGenerator();
        }

        return self::$instance;
    }

    public function generateModels()
    {
        Artisan::call("config:cache"); // Update config cache so that new model config files are taken into consideration

        $models = ModelGeneratorConfigHelper::getInstance()->getModels();
        $namespace = ModelGeneratorConfigHelper::getInstance()->getNamespace();
        $outputpath = ModelGeneratorConfigHelper::getInstance()->getOutputpath();

        foreach ($models as $model) {
            $this->generateModel($model, $outputpath, $namespace);
        }
    }

    private function generateModel($model, $outputpath, $namespace)
    {
        $modelname = $model['name'];
        // Cannot pass key-less parameters to an artisan call. Fortunately the class-name key was a key that could be used.
        // Discovered by looking at the error message thrown by the command when passed the wrong parameters
        $options = ["class-name" => $modelname, "--output-path" => app_path($outputpath), "--namespace" => $namespace];

        if (isset($model['table'])) {
            $options["--table-name"] = $model['table'];
        }

        Artisan::call("krlove:generate:model", $options);

        $this->adjustModel($model, $outputpath);
    }

    private function adjustModel(array $model, $outputpath)
    {
        $name = $model['name'];
        $modelpath = app_path($outputpath . DIRECTORY_SEPARATOR . "$name.php");

        if (ModelGeneratorConfigHelper::getInstance()->doesModelAdjustmentsExist($name)) {
            $classfilearray = array_merge($adjustArray = ModelGeneratorConfigHelper::getInstance()->getModelAdjustmentArray($name)
                , [ClassFileFactory::PATH_KEY => $modelpath]);
            $classfile = ClassFileFactory::getInstance()->createClassFileFromArray($classfilearray);
            $this->mergeExtraModelAdjustments($classfile, $model);
        } else {
            $classfile = ClassFileFactory::getInstance()->createClassfileFromArray([
                ClassFileFactory::PATH_KEY => $modelpath
            ]);
            $this->mergeExtraModelAdjustments($classfile, $model);
        }

        $this->classtailor->tailorClass($classfile);
    }

    private function mergeExtraModelAdjustments(ClassFile &$classfile, array &$model)
    {
        $classfile->mergeClassFile($this->commonclassfile);
        if (isset($model['extras'])) {
            foreach ($model['extras'] as $extra) {
                $classfile->mergeClassFile($this->qualifiedExtraClassfiles[$extra]);
            }
        }
    }

    private function initCommonClassFile()
    {
        if (ModelGeneratorConfigHelper::getInstance()->doesModelAdjustmentsExist(ModelGeneratorConfigHelper::COMMON_MODELNAME)) {
            $this->commonclassfile = ClassFileFactory::getInstance()->createClassfileFromArray(ModelGeneratorConfigHelper::getInstance()->getModelAdjustmentArray(ModelGeneratorConfigHelper::COMMON_MODELNAME));
        }
    }

    private function initExtraModelAdjustments()
    {
        $this->qualifiedExtraClassfiles = [];
        foreach (ModelGeneratorConfigHelper::getInstance()->getExtrasFilenames() as $filename) {
            $this->qualifiedExtraClassfiles[$filename] = ClassFileFactory::getInstance()->createClassfileFromArray(ModelGeneratorConfigHelper::getInstance()->getExtraModelAdjustmentArray($filename));
        }
    }
}
