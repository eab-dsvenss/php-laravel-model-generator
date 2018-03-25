<?php

namespace se\eab\php\laravel\modelgenerator\command;

use Illuminate\Console\Command;
use se\eab\php\laravel\modelgenerator\ModelGenerator;

class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eab-modelgenerator:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the specified methods';

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
        $modelgen = ModelGenerator::getInstance();
        $modelgen->generateModels();
    }
}
