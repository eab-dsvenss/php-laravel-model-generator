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
    private $qualifiers;

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
        $this->qualifiers = [];
        $this->qualifiedExtraClassfiles = [];
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

    public function generateModels(array $qualifiers)
    {
        $this->qualifiers = $qualifiers;
        $models = ConfigHelper::getModels();
        $namespace = ConfigHelper::getNamespace();
        $outputpath = ConfigHelper::getOutputpath();

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

        if (ConfigHelper::doesModelAdjustmentsExist($name)) {
            $classfilearray = array_merge($adjustArray = ConfigHelper::getModelAdjustmentArray($name)
                , [ClassFileFactory::PATH_KEY => $modelpath, ClassFileFactory::CLASSNAME_KEY => $name]);
            $classfile = ClassFileFactory::getInstance()->createClassFileFromArray($classfilearray);
            $this->mergeExtraModelAdjustments($classfile, $model);
        } else {
            $classfile = ClassFileFactory::getInstance()->createClassfileFromArray([
                ClassFileFactory::PATH_KEY => $modelpath,
                ClassFileFactory::CLASSNAME_KEY => $name
            ]);
            $this->mergeExtraModelAdjustements($classfile, $model);
        }

        $this->classtailor->tailorClass($classfile);
    }

    private function mergeExtraModelAdjustements(ClassFile &$classfile, array &$model)
    {
        $classfile->mergeClassFile($this->commonclassfile);
        foreach ($this->qualifiers as $q) {
            if (isset($model[$q]) && $model[$q]) {
                $classfile->mergeClassFile($this->qualifiedExtraClassfiles[$q]);
            }
        }
    }

    private function hasCommonClassFile()
    {
        return isset($this->commonclassfile);
    }

    private function initCommonClassFile()
    {
        if (ConfigHelper::doesModelAdjustmentsExist(ConfigHelper::COMMON_MODELNAME)) {
            $adjArray = array_merge(ConfigHelper::getModelAdjustmentArray(ConfigHelper::COMMON_MODELNAME), [ClassFileFactory::CLASSNAME_KEY => ConfigHelper::COMMON_MODELNAME]);
            $this->commonclassfile = ClassFileFactory::getInstance()->createClassfileFromArray($adjArray);
        }
    }

    public function appendToCommonClassfile(ClassFile &$classfile)
    {
        if ($this->hasCommonClassFile()) {
            $this->commonclassfile->mergeClassFile($classfile);
        } else {
            $this->commonclassfile = $classfile;
        }
    }

    public function appendToQualifiedExtraClassFile(ClassFile &$classfile, $qualifier)
    {
        if (isset($this->qualifiedExtraClassfiles[$qualifier])) {
            $this->qualifiedExtraClassfiles[$qualifier]->mergeClassFile($classfile);
        } else {
            $this->qualifiedExtraClassfiles[$qualifier] = $classfile;
        }
    }

    public function appendToModelAdjustmentsFile(ClassFile &$additions)
    {
        $classname = $additions->getClassName();
        if (ConfigHelper::doesModelAdjustmentsExist($classname)) {
            $classfile = ClassFileFactory::getInstance()->createClassfileFromArray(ConfigHelper::getModelAdjustmentArray($classname));
            $classfile->mergeClassFile($additions);
        } else {
            $classfile = $additions;
        }

        FileHandler::getInstance()->writeToFile(ConfigHelper::getAdjustmentsPath($classname), print_r(ClassFileFactory::getInstance()->getArrayFromClassFile($classfile)));
    }
}
