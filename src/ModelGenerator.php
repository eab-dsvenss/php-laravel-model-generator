<?php

namespace se\eab\php\laravel\modelgenerator;

use se\eab\php\laravel\modelgenerator\provider\ModelGeneratorServiceProvider;
use se\eab\php\classtailor\ClassTailor;
use se\eab\php\classtailor\factory\ClassFileFactory;
use Artisan;

/**
 * Description of ModelGenerator
 *
 * @author dsvenss
 */
class ModelGenerator
{

    private static $instance;
    private $classtailor;

    private function __construct()
    {
        $this->classtailor = new ClassTailor();
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
        $models = config(ModelGeneratorServiceProvider::CONFIG_FILENAME . ".models");
        $namespace = config(ModelGeneratorServiceProvider::CONFIG_FILENAME . ".namespace");
        $outputpath = config(ModelGeneratorServiceProvider::CONFIG_FILENAME . ".outputpath");

        foreach ($models as $model) {
            $this->generateModel($model, $outputpath, $namespace);
        }
    }

    private function generateModel($model, $outputpath, $namespace)
    {
        $modelname = $model['name'];
        $options = [$modelname, "--output-path" => app_path($outputpath), "--namespace" => $namespace];

        if (isset($model['table'])) {
            $options["--table-name"] = $model['table'];
        }
        Artisan::call("krlove:generate:model", $options);

        $this->adjustModel($modelname, $outputpath);
    }

    private function adjustModel($name, $outputpath)
    {

        if (file_exists(config_path(ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . DIRECTORY_SEPARATOR . "$name.php"))) {
            $classfilearray = array_merge($adjustArray = config(ModelGeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . ".$name")
                , ["path" => $outputpath . DIRECTORY_SEPARATOR . "$name.php"]);
            $classfile = ClassFileFactory::getInstance()->createClassFileFromArray($classfilearray);
            $this->classtailor->tailorClass($classfile);
        }
    }

}
