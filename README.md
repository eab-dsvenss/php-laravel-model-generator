# php-laravel-model-generator
Wrapper package for laravel model generation from database using [krlove/eloquent-model-generator](https://github.com/krlove/eloquent-model-generator)

# Installation

Get package
```
composer require eab-dsvenss/php-laravel-model-generator --dev
```
Register the provider in `app.php`

```
'providers' => [
    // ...
    se\eab\php\laravel\modelgenerator\provider\ModelGeneratorServiceProvider::class
];
```

```
php artisan eab-modelgenerator:install
```

# Usage

Specify models to generate in `app/config/eab-modelgeneratorconfig.php`

Specify the details for each model in `app/config/eab-modelgenerator/<modelname>.php`

Call `php artisan eab-modelgenerator:generate` to generate the models specified in the config file


# Configuration

## eab-modelgeneratorconfig.php

```
return [
    "namespace" => "App",
    "outputpath" => "model",
    "models" => [
        ["name" => "Dummy", <"table" => "DummyTable">, <"extras" => ["crud", "translatable"]>]
    ]
];
```

Each model can be decorated with extras. They are specified in the `extras`-array and point to files in `app/config/eab-modelgenerator/extras/<extrasname>.php`

The extras-file is formatted in the same way as any other modelconfig-file.

## Model config

Just remove dependencies, functions etc if you do not want them present in the tailored class
```
return [
    
    "dependencies" => ["dep1","dep2"],
    "removablefns" => [
        ["access" => "public", "name" => "dummyname", "content" => "dummycontent"]
    ],
    "functions" => [
<<<EOT
public function test() {
    \$test;
}
EOT
    ],
    "variables" => [
        ["access" => "public", "name" => "varname", <"value" => "some value">]
    ]
];
```

## Common Class Attributes 

If there are attributes that should be present in all classes, specify that in `app/config/eab-modelgenerator/EABCommon.php`

