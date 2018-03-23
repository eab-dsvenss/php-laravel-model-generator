<?php

namespace se\eab\php\laravel\modelgenerator;

use se\eab\php\laravel\modelgenerator\provider\EabModelgeneratorServiceProvider;
use se\eab\php\classtailor\ClassTailor;
use Config;
use se\eab\php\classtailor\model\ClassFile;
use se\eab\php\classtailor\factory\ClassFileFactory;

/**
 * Description of ModelGenerator
 *
 * @author dsvenss
 */
class ModelGenerator
{

    private static $instance;
    private $classtailor;

    private static function __construct()
    {
        $this->classtailor = new ClassTailor();
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new ModelGenerator();
        }

        return self::$instance;
    }

    public function generateModels()
    {
        $models = config(EabModelgeneratorServiceProvider::CONFIG_FILENAME . ".models");
        $namespace = config(EabModelgeneratorServiceProvider::CONFIG_FILENAME . ".namespace");
        $outputpath = config(EabModelgeneratorServiceProvider::CONFIG_FILENAME . ".outputpatch");

        foreach ($models as $model) {
            $this->generateModel($model, $outputpath, $namespace);
        }
    }

    private function generateModel($model, $outputpath, $namespace)
    {
        $modelname = $model['name'];
        $options = [$modelname, "--output-path" => $outputpath, "--namespace" => $namespace];

        if (isset($model['table'])) {
            $options["--table-name"] = $model['table'];
        }
        Artisan::call("krlove:generate:model", $options);

        $this->adjustModel($modelname);
    }

    private function adjustModel($name)
    {
        if (file_exists(config_path(EabModelgeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . DIRECTORY_SEPARATOR . "$name.php"))) {
            $classfile = ClassFileFactory::getInstance()->createClassFileFromArray(config(EabModelgeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . ".$name"));
            $this->classtailor->tailorClass($classfile);
        }
    }

    

}
