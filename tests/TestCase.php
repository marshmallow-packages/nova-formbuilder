<?php

namespace Marshmallow\NovaFormbuilder\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Marshmallow\NovaFormbuilder\NovaFormbuilderServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Marshmallow\\NovaFormbuilder\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            NovaFormbuilderServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_nova-formbuilder_table.php.stub';
        $migration->up();
        */
    }
}
