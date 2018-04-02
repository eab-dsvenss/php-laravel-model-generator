<?php
/**
 * Created by IntelliJ IDEA.
 * User: dsvenss
 * Date: 2018-04-01
 * Time: 22:11
 */

namespace se\eab\php\laravel\modelgenerator\config;

use se\eab\php\laravel\modelgenerator\provider\ModelGeneratorServiceProvider;

class ConfigHelper
{

    const MODELTABLE = "table";
    const CRUD = "crud";
    const MODELNAME = "name";
    const MODELS = "models";
    const NAMESPACE = "namespace";
    const OUTPUTPATH = "outputpath";
    const COMMON_MODELNAME = "EABCommon";

    /**
     * @return array
     */
    public static function getModels()
    {
        return config(ModelGeneratorServiceProvider::CONFIG_FILENAME . "." . self::MODELS);
    }

    public static function getNamespace()
    {
        return config(ModelGeneratorServiceProvider::CONFIG_FILENAME . "." . self::NAMESPACE);
    }

    public static function getOutputpath()
    {
        return config(ModelGeneratorServiceProvider::CONFIG_FILENAME . "." . self::OUTPUTPATH);
    }

    public static function getModelAdjustmentArray($name)
    {
        return config(ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . ".$name");
    }

    public static function getAdjustmentsPath($name = NULL) {
        $path = config(ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME);
        if (isset($name)) {
            $path .= DIRECTORY_SEPARATOR . "$name.php";
        }

        return $path;
    }

    public static function doesModelAdjustmentsExist($name)
    {
        return file_exists(config_path(ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . DIRECTORY_SEPARATOR . "$name.php"));
    }
}