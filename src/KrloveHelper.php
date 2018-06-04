<?php
/**
 * Created by IntelliJ IDEA.
 * User: dsvenss
 * Date: 2018-06-04
 * Time: 17:15
 */

namespace se\eab\php\laravel\modelgenerator;

use se\eab\php\laravel\modelgenerator\config\ModelGeneratorConfigHelper;
use Artisan;

class KrloveHelper
{

    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new KrloveHelper();
        }

        return self::$instance;
    }

    public function runGenerateCommand(array $model)
    {
        $modelname = $model[ModelGeneratorConfigHelper::MODELNAME_KEY];
        // Cannot pass key-less parameters to an artisan call. Fortunately the class-name key was a key that could be used.
        // Discovered by looking at the error message thrown by the command when passed the wrong parameters
        $options = ["class-name" => $modelname];

        $options["--table-name"] = ModelGeneratorConfigHelper::getInstance()->getTableForModel($model);

        Artisan::call("krlove:generate:model", $options);
    }
}