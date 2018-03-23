# php-laravel-model-generator
Wrapper package for laravel model generation from database using [krlove/eloquent-model-generator](https://github.com/krlove/eloquent-model-generator)

# Installation

```
composer require eab-dsvenss/php-laravel-model-generator --dev
php artisan eab-modelgenerator:install
```

# Usage

Specify models to generate in `eab-modelgenconfig`

Call `php artisan eab-modelgenerator:generate` to generate the models specified in the config file

Update the "Dummy"-model and remove the adjustmentsfile

# Configuration

TODO continue to specify configuration options