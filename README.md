# This is my package filament-notification-wizard

[![Latest Version on Packagist](https://img.shields.io/packagist/v/donorservices/filament-notification-wizard.svg?style=flat-square)](https://packagist.org/packages/donorservices/filament-notification-wizard)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/donorservices/filament-notification-wizard/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/donorservices/filament-notification-wizard/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/donorservices/filament-notification-wizard/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/donorservices/filament-notification-wizard/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/donorservices/filament-notification-wizard.svg?style=flat-square)](https://packagist.org/packages/donorservices/filament-notification-wizard)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require donorservices/filament-notification-wizard
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-notification-wizard-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-notification-wizard-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-notification-wizard-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filamentNotificationWizard = new Donorservices\FilamentNotificationWizard();
echo $filamentNotificationWizard->echoPhrase('Hello, Donorservices!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Azriel Walston](https://github.com/donorservices)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
