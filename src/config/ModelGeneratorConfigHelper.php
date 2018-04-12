<?php
/**
 * Created by IntelliJ IDEA.
 * User: dsvenss
 * Date: 2018-04-01
 * Time: 22:11
 */

namespace se\eab\php\laravel\modelgenerator\config;

use se\eab\php\classtailor\model\FileHandler;
use se\eab\php\laravel\modelgenerator\provider\ModelGeneratorServiceProvider;
use Log;
use se\eab\php\laravel\util\misc\ArrayStringBuilder;

class ModelGeneratorConfigHelper
{

    const MODELTABLE_KEY = "table";
    const MODELNAME_KEY = "name";
    const MODELS_KEY = "models";
    const NAMESPACE_KEY = "namespace";
    const OUTPUTPATH_KEY = "outputpath";
    const COMMON_MODELNAME = "EABCommon";
    const MODELEXTRAS_KEY = "extras";


    private $extrasfolder;

    private static $instance;

    private function __construct()
    {
        $this->extrasfolder = config_path(ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . DIRECTORY_SEPARATOR . ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_EXTRAFOLDER);
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new ModelGeneratorConfigHelper();
        }

        return self::$instance;
    }

    /**
     * @return array
     */
    public function getModels()
    {
        return config(ModelGeneratorServiceProvider::CONFIG_FILENAME . "." . self::MODELS_KEY);
    }

    public function getNamespace()
    {
        return config(ModelGeneratorServiceProvider::CONFIG_FILENAME . "." . self::NAMESPACE_KEY);
    }

    public function getOutputpath()
    {
        return config(ModelGeneratorServiceProvider::CONFIG_FILENAME . "." . self::OUTPUTPATH_KEY);
    }

    public function getModelAdjustmentArray($name)
    {
        return config(ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . ".$name");
    }

    public function getExtraModelAdjustmentArray($name)
    {
        return config(ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . "." . ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_EXTRAFOLDER . ".$name");
    }

    public function getAdjustmentsPath($name = NULL)
    {
        $path = config(ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME);
        if (isset($name)) {
            $path .= DIRECTORY_SEPARATOR . "$name.php";
        }

        return $path;
    }

    public function hasExtrasQualifier(array $model, $qualifier)
    {
       return isset($model[ModelGeneratorConfigHelper::MODELEXTRAS_KEY]) && in_array($qualifier, $model[ModelGeneratorConfigHelper::MODELEXTRAS_KEY]);
    }

    public function doesModelAdjustmentsExist($name)
    {
        return file_exists(config_path(ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . DIRECTORY_SEPARATOR . "$name.php"));
    }


    public function getExtrasFilenames()
    {
        $files = scandir($this->extrasfolder);
        if ($files) {
            return array_filter($files, function ($item) {
                return !is_dir($this->extrasfolder . DIRECTORY_SEPARATOR . $item);
            });
        }
        return [];
    }

    public function saveExtraModelAdjustmentsToFile(array $adjustments, $filename)
    {
        if (!file_exists($this->extrasfolder)) {
            if (!mkdir($this->extrasfolder)) {
                Log::warning("Could not create folder " . $this->extrasfolder);
            }
        }

        $adjustmentsstr = ArrayStringBuilder::getInstance()->arrayToString($adjustments);
        $filecontent = <<<EOT
<?php
return $adjustmentsstr;
EOT;

        return FileHandler::getInstance()->writeToFile($this->extrasfolder . DIRECTORY_SEPARATOR . "$filename.php", $filecontent);
    }


}