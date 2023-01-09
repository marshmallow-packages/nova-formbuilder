# Build wizards, forms & more for Laravel using Laravel Nova & TALL stack

[![Latest Version on Packagist](https://img.shields.io/packagist/v/marshmallow/nova-formbuilder.svg?style=flat-square)](https://packagist.org/packages/marshmallow/nova-formbuilder)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/marshmallow-packages/nova-formbuilder/run-tests?label=tests)](https://github.com/marshmallow/nova-formbuilder/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/marshmallow-packages/nova-formbuilder/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/marshmallow-packages/nova-formbuilder/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/marshmallow/nova-formbuilder.svg?style=flat-square)](https://packagist.org/packages/marshmallow/nova-formbuilder)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require marshmallow/nova-formbuilder
```

Install with

```bash
php artisan nova-formbuilder:install
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="nova-formbuilder-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="nova-formbuilder-config"
```

Add alpine & livewire & add this after loading scripts:

```php
@include('nova-formbuilder::alpine-tooltip')
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="nova-formbuilder-views"
php artisan vendor:publish --provider="Marshmallow\NovaFormbuilder\FormComponentsServiceProvider" --tag="marshmallow-views"
```

Add it to NovaServiceProvider.php

```php
MenuSection::make(__('Forms'), [
    MenuItem::resource(\Marshmallow\NovaFormbuilder\Nova\Form::class),
    MenuItem::resource(\Marshmallow\NovaFormbuilder\Nova\Step::class),
    MenuItem::resource(\Marshmallow\NovaFormbuilder\Nova\Question::class),
    MenuItem::resource(\Marshmallow\NovaFormbuilder\Nova\QuestionAnswer::class),
    MenuItem::resource(\Marshmallow\NovaFormbuilder\Nova\QuestionAnswerOption::class),
])->icon('clipboard-list')->collapsable(),
```

## Usage

```php
<livewire:mm-forms-form :form_id="$form_id" />
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Marshmallow](https://github.com/marshmallow-packages)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## TODO

[] set configs & explanation for external packages like: Livewire, Alpine, Spatie Media, laravel-honeypot & sortable
[] remove media lib pro (make custom extension)
[] Add translations
[] add or remove Flex layouts ???
[] Add custom fields
[] add JS & Css
[] remove Ray calls (only in debug)
[] add Tooltip alpineJs

// EXTRA
[] Create submissable form model like the org Notifiable
[] convert to formsubmit response
[] Set models in config
[] Set Livewire traits to implements
[] Remove nova restrictions per resource (authorizedToDelete etc.)
[] Make nova resources extendable
