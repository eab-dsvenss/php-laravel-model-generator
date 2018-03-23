<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace se\eab\php\laravel\modelgenerator;

use se\eab\php\laravel\modelgenerator\provider\EabModelGeneratorServiceProvider;
use se\eab\php\classtailor\ClassTailor;

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
        $models = config(EabModelGeneratorServiceProvider::CONFIG_FILENAME . ".models");
        $namespace = config(EabModelGeneratorServiceProvider::CONFIG_FILENAME . ".namespace");
        $outputpath = config(EabModelGeneratorServiceProvider::CONFIG_FILENAME . ".outputpatch");

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
    
    private function adjustModel($name) {
        // CONTINUE to access files in the created config folder which contains a file for each model that should be adjusted
    }

}
