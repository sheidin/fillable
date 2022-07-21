# Generate fillable attribute for eloquent models


This package will help you to fill the fillabel attribute within eloquent model


## Installation

You can install the package via composer:

```bash
composer require sheidin/fillable
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="fillable"
```

This is the contents of the published config file:

```php
return [
    'models_directory' => env('MODELS_DIR', app_path('Models')),
    'ignore_columns' => ['id'],
];
```
## Usage

```bash
php artisan model:fillable
or
php artisan model:fillable {MODEL_NAME}

```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
