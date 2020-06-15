Laravel Exchange Web Services Driver
====

A Mail Driver with support for Exchange Web Services, using the original Laravel API. This library extends the original Laravel classes, so it uses exactly the same methods.
This package requires a access to a EWS host.

This library uses the [php-ews](https://github.com/jamesiarmes/php-ews/) library to connect to the exchange web services host.
Therefore requires the following dependencies:

* Composer
* PHP 5.4 or greater
* cURL with NTLM support (7.30.0+ recommended)
* Exchange 2007 or later

For more information, visit that [repo](https://github.com/jamesiarmes/php-ews/)

# Install (Laravel)

Add the package to your composer.json and run composer update.
```json
"require": {
    "adeboyed/laravel-exchange-driver": "~1.0"
},
```

or installed with composer
```
$ composer require adeboyed/laravel-exchange-driver
```

Add the Exchange service provider in config/app.php:
(Laravel 5.5+ uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.)
```php
'providers' => [
    Adeboyed\LaravelExchangeDriver\ExchangeAddedServiceProvider::class
];
```

# Install (Lumen)

Add the package to your composer.json and run composer update.
```json
"require": {
    "adeboyed/laravel-exchange-driver": "~1.0"
},
```

or installed with composer
```bash
$ composer require adeboyed/laravel-exchange-driver
```

Add the sendgrid service provider in bootstrap/app.php
```php
$app->configure('mail');
$app->configure('services');
$app->register(Adeboyed\LaravelExchangeDriver\ExchangeServiceProvider::class);

unset($app->availableBindings['mailer']);
```

Create mail config files.
config/mail.php
```php
<?php
return [
    'driver' => env('MAIL_DRIVER', 'exchange'),
];
```

## Configure

.env
```
MAIL_DRIVER=exchange
MAIL_HOST=webmail.example.com
MAIL_USERNAME=examplemail
MAIL_PASSWORD=examplepassword
MAIL_MESSAGE_DISPOSITION_TYPE=SaveOnly|SendAndSaveCopy|SendOnly
```

config/mail.php (In using lumen, require creating config directory and file.)
```php
    'mailers' => [
        'exchange' => [
            'transport' => 'exchange',
            'host' => env('MAIL_HOST'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'messageDispositionType' => env('MAIL_MESSAGE_DISPOSITION_TYPE') // Optional, default: SendAndSaveCopy
        ],
    ],
```

For more information on the Message Disposition Type, [view more here](https://github.com/jamesiarmes/php-ews/blob/master/src/Enumeration/MessageDispositionType.php)