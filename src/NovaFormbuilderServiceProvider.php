<?php

namespace Marshmallow\NovaFormbuilder;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NovaFormbuilderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('nova-formbuilder')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_nova-formbuilder_table');
    }
}
