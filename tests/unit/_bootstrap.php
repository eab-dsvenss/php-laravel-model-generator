<?php
/**
 * Created by IntelliJ IDEA.
 * User: dsvenss
 * Date: 2018-04-03
 * Time: 11:49
 */

$kernel = \AspectMock\Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'includePaths' => [__DIR__.'/../../src', __DIR__ . "/../../vendor/laravel/framework/src",
        __DIR__ . "/../../vendor/eab-dsvenss/php-classtailor/src"],
    'cacheDir'  => '/tmp/php-laravel-model-generator',
]);