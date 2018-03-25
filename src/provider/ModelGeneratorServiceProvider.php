<?php

namespace se\eab\php\laravel\modelgenerator\provider;

use Illuminate\Support\ServiceProvider;

class ModelGeneratorServiceProvider extends ServiceProvider
{

    const CONFIG_FILENAME = "eab-modelgenconfig";
    const MODEL_ADJUSTMENTS_FOLDERNAME = "eab-modelgenerator";

    private $basepath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->basepath . "config" . DIRECTORY_SEPARATOR . EabModelgeneratorServiceProvider::CONFIG_FILENAME . ".php" => config_path(EabModelgeneratorServiceProvider::CONFIG_FILENAME . '.php'),
            $this->basepath . "config" . DIRECTORY_SEPARATOR . EabModelgeneratorServiceProvider::CONFIG_FILENAME . ".php" => config_path(EabModelgeneratorServiceProvider::MODEL_ADJUSTMENTS_FOLDERNAME . 'Dummy.php')
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // It is essential to check that a class exists if there is a possibility that it does not. Othe rwise errors will be triggered
        if (class_exists("Krlove\EloquentModelGenerator\Provider\GeneratorServiceProvider")) {
            $this->app->register('Krlove\EloquentModelGenerator\Provider\GeneratorServiceProvider');
        }
    }

}
