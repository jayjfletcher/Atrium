<?php

declare(strict_types=1);

namespace Atrium\Atrium\Tests;

use Atrium\Atrium\AtriumServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Concerns\WithLaravelMigrations;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;
    use WithLaravelMigrations;

    protected function getPackageProviders($app): array
    {
        return [
            AtriumServiceProvider::class,
        ];
    }

    /**
     * Pretend the app is running in a non-local environment for the duration
     * of a test, without letting the database teardown prompt for
     * confirmation the way a real production run would.
     */
    protected function withEnvironment(string $environment): void
    {
        $this->app->detectEnvironment(fn (): string => $environment);

        $this->app->make(ConsoleKernel::class);
        $this->app['env'] = $environment;

        $this->beforeApplicationDestroyed(function (): void {
            $this->app->detectEnvironment(fn (): string => 'testing');
            $this->app['env'] = 'testing';
        });
    }

    protected function defineEnvironment($app): void
    {
        tap($app->make(Repository::class), function (Repository $config): void {
            $config->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

            $config->set('view.paths', array_merge(
                (array) $config->get('view.paths', []),
                [__DIR__.'/Fixtures/views'],
            ));

            $config->set('database.default', 'testing');
            $config->set('database.connections.testing', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                // Cascade deletes only apply when SQLite enforces them.
                'foreign_key_constraints' => true,
            ]);
        });
    }
}
