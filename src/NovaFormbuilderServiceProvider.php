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
            ->hasMigrations([
                '01_create_nova_formbuilder_forms_table',
                '02_create_nova_formbuilder_form_formsubmissions_table',
                '03_create_nova_formbuilder_steps_table',
                '04_create_nova_formbuilder_questions_table',
                '05_create_nova_formbuilder_question_answers_table',
                '06_create_nova_formbuilder_question_answer_options_table',
            ]);
    }
}
