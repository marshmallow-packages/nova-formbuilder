# Build wizards, forms & more for Laravel using Laravel Nova & TALL stack

[![Latest Version on Packagist](https://img.shields.io/packagist/v/marshmallow/nova-formbuilder.svg?style=flat-square)](https://packagist.org/packages/marshmallow/nova-formbuilder)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/marshmallow/nova-formbuilder/run-tests?label=tests)](https://github.com/marshmallow/nova-formbuilder/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/marshmallow/nova-formbuilder/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/marshmallow/nova-formbuilder/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/marshmallow/nova-formbuilder.svg?style=flat-square)](https://packagist.org/packages/marshmallow/nova-formbuilder)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/nova-formbuilder.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/nova-formbuilder)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

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

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="nova-formbuilder-views"
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

[] check if setting depends_on_question & still works
[] check if setting validation rules & still works

[] check custom blade components (mm)
[] check component classes
[] check livewire units & views
[] check livewire custom components

[] set configs & explanation for external packages like: spatie/laravel-honeypot & sortable
[] remove media lib pro (make custom extension)
[] add or remove Flex layouts ???

[] add JS & Css
[] remove Ray calls (only in debug)
[] add Tooltip alpineJs
[] add all deps to readme (alpine, livewire etc.)

// EXTRA
[] create submissable form model like the org Notifiable
[] convert to formsubmit response
[] set models in config
[] set Livewire traits to implements
[] remove nova restrictions per resource (authorizedToDelete etc.)
[] make nova resources extendable
