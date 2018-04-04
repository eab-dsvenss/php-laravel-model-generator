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

class ModelGeneratorConfigHelper
{

    const MODELTABLE = "table";
    const CRUD = "crud";
    const MODELNAME = "name";
    const MODELS = "models";
    const NAMESPACE = "namespace";
    const OUTPUTPATH = "outputpath";
    const COMMON_MODELNAME = "EABCommon";


    private $extrasfolder;

    private static $instance;

    private function __construct()
    {
        $this->extrasfolder = config_path(ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . DIRECTORY_SEPARATOR . ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_EXTRAFOLDER);
    }

    public static function getInstance() {
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
        return config(ModelGeneratorServiceProvider::CONFIG_FILENAME . "." . self::MODELS);
    }

    public function getNamespace()
    {
        return config(ModelGeneratorServiceProvider::CONFIG_FILENAME . "." . self::NAMESPACE);
    }

    public function getOutputpath()
    {
        return config(ModelGeneratorServiceProvider::CONFIG_FILENAME . "." . self::OUTPUTPATH);
    }

    public function getModelAdjustmentArray($name)
    {
        return config(ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . ".$name");
    }

    public function getAdjustmentsPath($name = NULL)
    {
        $path = config(ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME);
        if (isset($name)) {
            $path .= DIRECTORY_SEPARATOR . "$name.php";
        }

        return $path;
    }

    public function doesModelAdjustmentsExist($name)
    {
        return file_exists(config_path(ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . DIRECTORY_SEPARATOR . "$name.php"));
    }



    public function getExtrasFilenames()
    {
        $files = scandir($this->extrasfolder);
        if ($files) {
            return array_filter($files, function($item) {
                return !is_dir($this->extrasfolder . DIRECTORY_SEPARATOR . $item);
            });
        }
        return [];
    }

    public function saveExtraModelAdjustmentsToFile(array $adjustments, $filename) {
        if (!file_exists($this->extrasfolder)) {
            if(!mkdir($this->extrasfolder)) {
                Log::warning("Could not create folder " . $this->extrasfolder);
            }
        }
        FileHandler::getInstance()->writeToFile($this->extrasfolder . DIRECTORY_SEPARATOR . "$filename.php", "return " . print_r($adjustments, true));
    }
}