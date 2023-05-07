<?php

namespace Igniter\Api\Tests;

use function Orchestra\Testbench\artisan;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function defineEnvironment($app)
    {
        $app['config']->set('database.connections.mysql.strict', false);
    }

    protected function getPackageProviders($app)
    {
        return [
            \Igniter\Flame\ServiceProvider::class,
            \Igniter\Api\Extension::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        artisan($this, 'igniter:up');
    }
}
