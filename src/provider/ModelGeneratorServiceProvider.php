<?php

namespace se\eab\php\laravel\modelgenerator\provider;

use Illuminate\Support\ServiceProvider;
use se\eab\php\laravel\modelgenerator\command\GenerateCommand;
use se\eab\php\laravel\modelgenerator\command\InstallCommand;

class ModelGeneratorServiceProvider extends ServiceProvider
{

    const CONFIG_FILENAME = "eab-modelgeneratorconfig";
    const DUMMY_ADJUSTMENT_FILENAME = "Dummy.php";
    const MODEL_ADJUSTMENTS_FOLDERNAME = "eab-modelgenerator";
    const MODEL_ADJUSTMENTS_EXTRAFOLDER = "extras";

    private $basepath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->basepath . "config" . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME . ".php" => config_path(self::CONFIG_FILENAME . '.php'),
            $this->basepath . "config" . DIRECTORY_SEPARATOR . self::DUMMY_ADJUSTMENT_FILENAME => config_path(self::MODEL_ADJUSTMENTS_FOLDERNAME . DIRECTORY_SEPARATOR . self::DUMMY_ADJUSTMENT_FILENAME)
        ]);
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                GenerateCommand::class
            ]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // It is essential to check that a class exists if there is a possibility that it does not. Otherwise errors will be triggered
        if (class_exists("Krlove\\EloquentModelGenerator\\Provider\\GeneratorServiceProvider")) {
            $this->app->register('Krlove\\EloquentModelGenerator\\Provider\\GeneratorServiceProvider');
        }

        if (class_exists("\\Reliese\\Coders\\CodersServiceProvider")) {
            $this->app->register("\\Reliese\\Coders\\CodersServiceProvider");
        }

        if (class_exists("Way\Generators\GeneratorsServiceProvider") &&
          class_exists("Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider") &&
          class_exists("User11001\EloquentModelGenerator\EloquentModelGeneratorProvider")) {
            $this->app->register("Way\Generators\GeneratorsServiceProvider");
            $this->app->register("Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider");
            $this->app->register("User11001\EloquentModelGenerator\EloquentModelGeneratorProvider");
        }
    }

}
