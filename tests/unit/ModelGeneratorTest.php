<?php

use se\eab\php\laravel\modelgenerator\config\ModelGeneratorConfigHelper;
use se\eab\php\laravel\modelgenerator\ModelGenerator;
use AspectMock\Test as test;
use se\eab\php\classtailor\factory\ClassFileFactory;
use se\eab\php\classtailor\ClassTailor;

class ModelGeneratorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    private $confighelper_mock;
    private $modelgen;
    private $models;
    private $namespace;
    private $outputpath;
    private $commonadjustments;
    private $extraadjustments;
    private $adjustments;

    /**
     * @throws Exception
     */
    protected function _before()
    {
        $this->setupData();
        $this->setupMocks();
    }

    protected function _after()
    {
    }

    private function setupData()
    {
        $this->modelgen = ModelGenerator::getInstance();
        $this->models = [
            ["name" => "Dummy", "table" => "DummyTable1", "extras" => ["extra1"]],
            ["name" => "Dummy2", "table" => "DummyTable2"],
        ];
        $this->namespace = "namespace";
        $this->outputpath = "outputpath";
        $this->adjustments = [
            ClassFileFactory::REPLACEABLES_KEY => [
                [ClassFileFactory::PATTERN_KEY => "pattern1", ClassFileFactory::REPLACEMENT_KEY => "replacement1"]
            ]
        ];
        $this->commonadjustments = [
            ClassFileFactory::DEPENDENCIES_KEY => ["commondep1"]
        ];
        $this->extraadjustments = [
            ClassFileFactory::VARIABLES_KEY => [
                [ClassFileFactory::NAME_KEY => "extra1", ClassFileFactory::ACCESS_KEY => "public"]
            ]
        ];
    }

    /**
     * @throws Exception
     */
    private function setupMocks()
    {
        test::func('se\eab\php\laravel\modelgenerator', "app_path", "path");
        test::double('Artisan', ["call" => true]);
        $this->confighelper_mock = test::double(ModelGeneratorConfigHelper::class, [
            "getModels" => $this->models,
            "getNamespace" => $this->namespace,
            "getOutputpath" => $this->outputpath,
            "getModelAdjustmentArray" => function ($name) {
                if ($name == ModelGeneratorConfigHelper::COMMON_MODELNAME) {
                    return $this->commonadjustments;
                } else {
                    return $this->adjustments;
                }
            },
            "getExtrasFilenames" => ["extra1"],
            "doesModelAdjustmentsExist" => function ($name) {
                if ($name == "Dummy") {
                    return false;
                } elseif ($name == ModelGeneratorConfigHelper::COMMON_MODELNAME) {
                    return true;
                } else {
                    return true;
                }
            },
            "getExtraModelAdjustmentArray" => function ($name) {
                return $this->extraadjustments;

            }
        ]);
        test::methods($this->confighelper_mock, [
            'getModels',
            'getNamespace',
            "getOutputpath",
            'getModelAdjustmentArray',
            'getExtrasFilenames',
            'doesModelAdjustmentsExist',
            "getExtraModelAdjustmentArray"
        ]);
        test::double(ClassTailor::class, [
            "tailorClass", true
        ]);
    }

    // tests
    public function testGenerateModels()
    {
        ModelGenerator::getInstance()->generateModels();
        $this->assertTrue(true);
    }
}