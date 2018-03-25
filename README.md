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

Specify models to generate in `eab-modelgenconfig`

Call `php artisan eab-modelgenerator:generate` to generate the models specified in the config file

Update the "Dummy"-model and remove the adjustmentsfile

# Configuration

## Config example

```
return [
    "namespace" => "App",
    "outputpath" => "model",
    "models" => [
        ["name" => "Dummy", <"table" => "DummyTable">]
    ]
];
```

## Model tailoring example
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
        ["access" => "public", "name" => "varname"]
    ]
];
```
