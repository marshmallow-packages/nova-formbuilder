<?php

namespace Marshmallow\NovaFormbuilder;

use Livewire\Livewire;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Illuminate\View\Compilers\BladeCompiler;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Marshmallow\NovaFormbuilder\Http\Livewire\Forms\Form;
use Marshmallow\NovaFormbuilder\Http\Livewire\Forms\Step;

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
                'create_nova_formbuilder_forms_table',
                'create_nova_formbuilder_form_submissions_table',
                'create_nova_formbuilder_steps_table',
                'create_nova_formbuilder_questions_table',
                'create_nova_formbuilder_question_answers_table',
                'create_nova_formbuilder_question_answer_options_table',
            ])
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->startWith(function (InstallCommand $command) {
                    $command->info('Hello, and welcome to the greatest Nova Form Builder package!');
                })
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('marshmallow-packages/nova-formbuilder')
                    ->endWith(function (InstallCommand $command) {
                        $command->info('Have an awesome day!');
                    });
            });


        $this->configureComponents();
    }


    /**
     * Configure the Marshmallow Blade components.
     *
     * @return void
     */
    protected function configureComponents()
    {

        $this->callAfterResolving(BladeCompiler::class, function () {
            /**
             * Form fields
             */
            $this->registerComponent('error');
            $this->registerComponent('divider');
            $this->registerComponent('action-message');

            $this->registerComponent('buttons.button');

            $this->registerComponent('labels.label');
            $this->registerComponent('labels.tooltip-label');
            $this->registerComponent('labels.tooltip');

            $this->registerComponent('questions.default-input');
            $this->registerComponent('questions.checkbox');
            $this->registerComponent('questions.radio');
            $this->registerComponent('questions.input');
            $this->registerComponent('questions.select');
            $this->registerComponent('questions.textarea');
        });

        Livewire::component('mm-forms-form', Form::class);
        Livewire::component('mm-forms-step', Step::class);
    }

    /**
     * Register the given component.
     *
     * @param  string  $component
     * @return void
     */
    protected function registerComponent(string $component)
    {
        Blade::component('marshmallow::components.' . $component, 'mm-forms-' . $component);
    }
}
