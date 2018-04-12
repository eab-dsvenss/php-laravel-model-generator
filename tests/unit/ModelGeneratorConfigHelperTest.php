<?php

/*namespace se\eab\php\laravel\modelgenerator\config;*/

use AspectMock\Test as test;
use se\eab\php\laravel\modelgenerator\config\ModelGeneratorConfigHelper;

class ModelGeneratorConfigHelperTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        parent::_before();
    }

    protected function _after()
    {
        parent::_after();
    }

    private function setupMocks()
    {

    }

    private function setConfigMock($returnval)
    {
        test::func("se\\eab\\php\\laravel\\modelgenerator\\config", "config", $returnval);
        test::func("se\\eab\\php\\laravel\\modelgenerator\\config", "config_path", "path");
        test::func("se\\eab\\php\\laravel\\modelgenerator\\config", "file_exists", $returnval);
        test::func("se\\eab\\php\\laravel\\modelgenerator\\config", "scandir", $returnval);
        test::func('se\eab\php\classtailor\model', "file_put_contents", true);
    }

    // tests
    public function testGetModels()
    {
        $models = [
            "test",
            "test2"
        ];
        $this->setConfigMock($models);

        $actualmodels = ModelGeneratorConfigHelper::getInstance()->getModels();
        $this->assertEquals($models, $actualmodels);
    }

    public function testGetNamespace()
    {
        $namespace = "namespace";
        $this->setConfigMock($namespace);

        $actualnamespace = ModelGeneratorConfigHelper::getInstance()->getNamespace();
        $this->assertEquals($namespace, $actualnamespace);
    }

    public function testGetOutputpath()
    {
        $outputpath = "outputpath";
        $this->setConfigMock($outputpath);

        $actualoutputpath = ModelGeneratorConfigHelper::getInstance()->getOutputpath();
        $this->assertEquals($outputpath, $actualoutputpath);
    }

    public function testGetModelAdjustmentArray()
    {
        $name = "test";
        $modeladjarr = [
            "test",
            "test2"
        ];
        $this->setConfigMock($modeladjarr);

        $actualmodeladjarr = ModelGeneratorConfigHelper::getInstance()->getModelAdjustmentArray($name);
        $this->assertEquals($modeladjarr, $actualmodeladjarr);
    }

    public function testGetAdjustmentsPath()
    {
        $names = [
            NULL,
            "name1"
        ];
        $base = 'dummytext';
        $this->setConfigMock($base);

        foreach ($names as $name) {
            $actualname = ModelGeneratorConfigHelper::getInstance()->getAdjustmentsPath($name);
            if (!isset($name)) {
                $this->assertEquals("$base", $actualname);
            } else {
                $this->assertEquals("$base/$name.php", $actualname);
            }
        }
    }

    public function doesModelAdjustmentsExist()
    {
        $doesfileexist = [true, false];

        foreach ($doesfileexist as $dfe) {
            $this->setConfigMock($dfe);
            $this->assertEquals($dfe, ModelGeneratorConfigHelper::getInstance()->doesModelAdjustmentsExist("test"));
        }

    }

    public function testGetExtrasFilenames()
    {
        $filenames = [
            FALSE,
            ["name1", "name2"]
        ];

        foreach ($filenames as $fnames) {
            $this->setConfigMock($fnames);

            $actualfilenames = ModelGeneratorConfigHelper::getInstance()->getExtrasFilenames();

            if (!$fnames) {
                $this->assertEquals([], $actualfilenames);
            } else {
                $this->assertEquals($fnames, $actualfilenames);
            }
        }

    }

    public function testHasExtrasQualifier()
    {
        $model = [ModelGeneratorConfigHelper::MODELEXTRAS_KEY => ["test", "tjena"]];

        $this->assertTrue(ModelGeneratorConfigHelper::getInstance()->hasExtrasQualifier($model, "tjena"));
        $this->assertFalse(ModelGeneratorConfigHelper::getInstance()->hasExtrasQualifier($model, "knas"));
    }

    public function testSaveExtraModelAdjustmentsToFile()
    {

        $this->setConfigMock(true);

        $array = [
            "test",
            "key1" => [
                "jaga",
                "key2" => ["hilli"]
            ]
        ];

        $this->assertTrue(ModelGeneratorConfigHelper::getInstance()->saveExtraModelAdjustmentsToFile($array, "test"));

    }


}