<?php

namespace se\eab\php\laravel\modelgenerator;

use se\eab\php\classtailor\model\ClassFile;
use se\eab\php\laravel\modelgenerator\library\KrloveHelper;
use se\eab\php\laravel\modelgenerator\library\PepijHelper;
use se\eab\php\laravel\modelgenerator\library\RelieseHelper;
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

        $lib = ModelGeneratorConfigHelper::getInstance()->getLibrary();


        if ($lib == ModelGeneratorConfigHelper::LIB_PEPIJ) {
            PepijHelper::getInstance()->runGenerateCommand();
        }

        foreach ($models as $model) {
            $this->generateModel($model);
        }
    }

    private function generateModel($model)
    {
        $lib = ModelGeneratorConfigHelper::getInstance()->getLibrary();
        switch ($lib) {
            case ModelGeneratorConfigHelper::LIB_KRLOVE:
                KrloveHelper::getInstance()->runGenerateCommand($model);
                break;
            case ModelGeneratorConfigHelper::LIB_PEPIJ:
                break;
            case ModelGeneratorConfigHelper::LIB_RELIESE:
            default:
                RelieseHelper::getInstance()->runGenerateCommand($model);
                break;
        }

        $this->adjustModel($model);
    }

    private function adjustModel(array $modelArray)
    {
        $name = $modelArray[ModelGeneratorConfigHelper::MODELNAME_KEY];

        if (ModelGeneratorConfigHelper::getInstance()->doesModelAdjustmentsExist($name)) {
            $classfile = $this->handleExistingModelAdjustment($modelArray);
        } else {
            $classfile = $this->handleNonExistentModelAdjustment($modelArray);
        }

        RelieseHelper::getInstance()->adjustClassfileForReliese($classfile, $modelArray);

        $this->classtailor->tailorClass($classfile);
    }

    private function handleExistingModelAdjustment(array $model)
    {
        $name = $model[ModelGeneratorConfigHelper::MODELNAME_KEY];
        $modelpath = ModelGeneratorConfigHelper::getInstance()->getOutputpathToModel($name);

        $classfilearray = array_merge($adjustArray = ModelGeneratorConfigHelper::getInstance()->getModelAdjustmentArray($name)
          , [ClassFileFactory::PATH_KEY => $modelpath]);
        $classfile = ClassFileFactory::getInstance()->createClassFileFromArray($classfilearray);
        $this->mergeExtraModelAdjustments($classfile, $model);

        return $classfile;
    }

    private function handleNonExistentModelAdjustment($model)
    {
        $name = $model[ModelGeneratorConfigHelper::MODELNAME_KEY];
        $modelpath = ModelGeneratorConfigHelper::getInstance()->getOutputpathToModel($name);

        $classfile = ClassFileFactory::getInstance()->createClassfileFromArray([
          ClassFileFactory::PATH_KEY => $modelpath
        ]);
        $this->mergeExtraModelAdjustments($classfile, $model);

        return $classfile;
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
