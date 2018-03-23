<?php

namespace se\eab\php\laravel\modelgenerator\command;

use Illuminate\Console\Command;
use Artisan;
use se\eab\php\laravel\modelgenerator\provider\EabModelgeneratorServiceProvider;

class EabModelgeneratorInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eab-modelgenerator:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the package and all dependencies';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Artisan::call("vendor:publish", ['--provider' => EabModelgeneratorServiceProvider::class]);
    }
}
