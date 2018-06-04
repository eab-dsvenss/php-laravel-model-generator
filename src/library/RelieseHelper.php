<?php
/**
 * Created by IntelliJ IDEA.
 * User: dsvenss
 * Date: 2018-06-04
 * Time: 17:15
 */

namespace se\eab\php\laravel\modelgenerator\library;


use se\eab\php\classtailor\model\ClassFile;
use se\eab\php\classtailor\model\content\VariableContent;
use se\eab\php\laravel\modelgenerator\config\ModelGeneratorConfigHelper;
use Artisan;

class RelieseHelper
{

    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new RelieseHelper();
        }

        return self::$instance;
    }

    public function adjustClassfileForReliese(ClassFile &$classfile, array $model)
    {
        if (ModelGeneratorConfigHelper::getInstance()->getLibrary() == ModelGeneratorConfigHelper::LIB_RELIESE) {
            $classfile->addVariable(new VariableContent("protected", "table",
              "'" . ModelGeneratorConfigHelper::getInstance()->getTableForModel($model) . "'"));
        }
    }

    public function runGenerateCommand($model)
    {
        $options = [
          "--table" => ModelGeneratorConfigHelper::getInstance()->getTableForModel($model)
        ];
        Artisan::call("code:models", $options);
    }
}