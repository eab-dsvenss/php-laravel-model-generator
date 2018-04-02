<?php

namespace se\eab\php\laravel\modelgenerator;

use se\eab\php\classtailor\model\ClassFile;
use se\eab\php\laravel\modelgenerator\provider\ModelGeneratorServiceProvider;
use se\eab\php\classtailor\ClassTailor;
use se\eab\php\classtailor\factory\ClassFileFactory;
use Artisan;
use se\eab\php\laravel\modelgenerator\config\ConfigHelper;
use se\eab\php\classtailor\model\FileHandler;

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

    private function __construct()
    {
        $this->classtailor = new ClassTailor();
        $this->modelconfigbasedir = ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . DIRECTORY_SEPARATOR;
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
        $models = ConfigHelper::getModels();
        $namespace = ConfigHelper::getNamespace();
        $outputpath = ConfigHelper::getOutputpath();

        $this->setCommonModel();

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

        $this->adjustModel($modelname, $outputpath);
    }

    private function adjustModel($name, $outputpath)
    {
        $modelpath = app_path($outputpath . DIRECTORY_SEPARATOR . "$name.php");
        if (ConfigHelper::doesModelAdjustmentsExist($name)) {
            $classfilearray = array_merge($adjustArray = ConfigHelper::getModelAdjustmentArray($name)
                , ["path" => $modelpath]);
            $classfile = ClassFileFactory::getInstance()->createClassFileFromArray($classfilearray);
            $classfile->mergeClassFile($this->commonclassfile);
            $this->classtailor->tailorClass($classfile);
        } else if (isset($this->commonclassfile)) {
            $this->commonclassfile->setPath($modelpath);
            $this->classtailor->tailorClass($this->commonclassfile);
        }
    }

    private function setCommonModel()
    {
        if (ConfigHelper::doesModelAdjustmentsExist(ConfigHelper::COMMON_MODELNAME)) {
            $this->commonclassfile = ClassFileFactory::getInstance()->createClassfileFromArray(ConfigHelper::getModelAdjustmentArray(ConfigHelper::COMMON_MODELNAME));
        }
    }

    public function appendToCommonModel(ClassFile &$classfile) {
        if (ConfigHelper::doesModelAdjustmentsExist(ConfigHelper::COMMON_MODELNAME)) {
            $commonClassfile = ClassFileFactory::getInstance()->createClassfileFromArray(ConfigHelper::getModelAdjustmentArray(ConfigHelper::COMMON_MODELNAME));
            $commonClassfile->mergeClassFile($classfile);
        } else {
            $commonClassfile = $classfile;
        }

        FileHandler::getInstance()->writeToFile(ConfigHelper::getAdjustmentsPath(ConfigHelper::COMMON_MODELNAME), print_r(ClassFileFactory::getInstance()->getArrayFromClassFile($commonClassfile)));
    }
}
