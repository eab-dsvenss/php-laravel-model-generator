<?php
/**
 * Created by IntelliJ IDEA.
 * User: dsvenss
 * Date: 2018-06-04
 * Time: 17:15
 */

namespace se\eab\php\laravel\modelgenerator\library;

use se\eab\php\laravel\modelgenerator\config\ModelGeneratorConfigHelper;
use Artisan;

class PepijHelper
{

    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new PepijHelper();
        }

        return self::$instance;
    }

    public function runGenerateCommand()
    {
        Artisan::call("models:generate", [
          "--path" => app_path(ModelGeneratorConfigHelper::getInstance()->getOutputpathFromConfig()),
          "--namespace" => ModelGeneratorConfigHelper::getInstance()->getNamespace(),
          "--overwrite" => true
        ]);
    }
}