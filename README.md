# Laravel 2C2P Payment Gateway Api & 123 Api

Laravel 2C2P package

## Installation

Install using composer:
```php
composer require php-junior/laravel-2c2p
```

Once installed, in your project's config/app.php file replace the following entry from the providers array:

```php
PhpJunior\Laravel2C2P\Laravel2C2PServiceProvider::class,
```

And 
```php 
php artisan vendor:publish --provider="PhpJunior\Laravel2C2P\Laravel2C2PServiceProvider"
```