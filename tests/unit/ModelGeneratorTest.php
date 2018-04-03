<?php

use se\eab\php\laravel\modelgenerator\config\ModelGeneratorConfigHelper;

use AspectMock\Test as test;

class ModelGeneratorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    private $confighelper_mock;
    private $models;
    private $namespace;
    private $outputpath;

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

    private function setupData() {
        $this->models = [
            ["name" => "Dummy", "table" => "DummyTable1", "crud" => true],
            ["name" => "Dummy2", "table" => "DummyTable2"],
            ["name" => "Dummy3", "table" => "DummyTable3", "crud" => true]
        ];
        $this->namespace = "namespace";
        $this->outputpath = "outputpath";
    }

    /**
     * @throws Exception
     */
    private function setupMocks() {
        $this->confighelper_mock = test::double(ModelGeneratorConfigHelper::getInstance()->class, [
            "getModels" => $this->models,
            "getNamespace" => $this->namespace,
            "getOutputpath" => $this->outputpath
        ]);
        test::methods($this->confighelper_mock, ['getModels']);
    }

    // tests
    public function testGenerateModels()
    {
        // TODO implement
    }
}