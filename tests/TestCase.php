<?php
/**
 * This file is part of the trendsoft/laravel-bookmark.
 * (c) trendsoft <hadi@trendsoft.org>
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests;

use Illuminate\Foundation\Application;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{

    /**
     *
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [];
    }

    /**
     * Define environment setup
     *
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => ''
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        $this->loadMigrationsFrom(dirname(__DIR__) . '/migrations');
    }
}