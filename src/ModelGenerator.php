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

        foreach ($models as $model) {
            $this->generateModel($model);
        }
    }

    private function generateModel($model)
    {
        $lib = ModelGeneratorConfigHelper::getInstance()->getLibrary();
        switch ($lib) {
            case "krlove":
                $modelname = $model[ModelGeneratorConfigHelper::MODELNAME_KEY];
                // Cannot pass key-less parameters to an artisan call. Fortunately the class-name key was a key that could be used.
                // Discovered by looking at the error message thrown by the command when passed the wrong parameters
                $options = ["class-name" => $modelname];

                if (isset($model[ModelGeneratorConfigHelper::MODELTABLE_KEY])) {
                    $options["--table-name"] = $model[ModelGeneratorConfigHelper::MODELTABLE_KEY];
                }

                Artisan::call("krlove:generate:model", $options);
                break;
            case "reliese":
                $options = [
                  "--table" => isset($model[ModelGeneratorConfigHelper::MODELTABLE_KEY]) ? $model[ModelGeneratorConfigHelper::MODELTABLE_KEY] : strtolower($model[ModelGeneratorConfigHelper::MODELNAME_KEY]) . "s"
                ];
                Artisan::call("code:models", $options);
                break;
            default:

                break;
        }


        $this->adjustModel($model);
    }

    private function adjustModel(array $model)
    {
        $name = $model[ModelGeneratorConfigHelper::MODELNAME_KEY];
        $modelpath = app_path(ModelGeneratorConfigHelper::getInstance()->getOutputpath() . DIRECTORY_SEPARATOR . "$name.php");

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
        if (isset($model[ModelGeneratorConfigHelper::MODELEXTRAS_KEY])) {
            foreach ($model[ModelGeneratorConfigHelper::MODELEXTRAS_KEY] as $extra) {
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
