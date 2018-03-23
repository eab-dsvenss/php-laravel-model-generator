<?php

namespace se\eab\php\laravel\modelgenerator\provider;

use Illuminate\Support\ServiceProvider;

class EabModelGeneratorServiceProvider extends ServiceProvider
{

    const CONFIG_FILENAME = "eab-modelgenconfig";

    private $basepath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->basepath . "config" . DIRECTORY_SEPARATOR . EabModelGeneratorServiceProvider::CONFIG_FILENAME . ".php" => config_path(EabModelGeneratorServiceProvider::CONFIG_FILENAME . '.php')
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
