<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Plugins;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
use JayI\Atrium\Domains\Plugins\Console\Commands\MakePluginCommand;
use JayI\Atrium\Domains\Plugins\Console\Commands\PluginListCommand;
use JayI\Atrium\Domains\Plugins\Services\ComposerPluginDiscovery;
use JayI\Atrium\Domains\Plugins\Services\PluginRegistry;
use JayI\Atrium\Support\ServiceProvider;

class PluginsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ComposerPluginDiscovery::class, fn (Application $app): ComposerPluginDiscovery => new ComposerPluginDiscovery(
            $app->make(Filesystem::class),
            $app->basePath('vendor'),
        ));

        $this->app->singleton(PluginRegistry::class, function (Application $app): PluginRegistry {
            $disabled = $app->make(Repository::class)->get('atrium.disabled', []);

            return new PluginRegistry($app)->disable(is_array($disabled) ? $disabled : []);
        });
    }

    public function boot(): void
    {
        $this->registerPlugins();

        $this->bootRoutes();

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakePluginCommand::class,
                PluginListCommand::class,
            ]);
        }
    }

    /**
     * Register the discovered and configured plugins.
     */
    private function registerPlugins(): void
    {
        $registry = $this->app->make(PluginRegistry::class);

        $config = $this->app->make(Repository::class);

        if ($config->get('atrium.discover', true) === true) {
            $registry->registerMany(
                $this->app->make(ComposerPluginDiscovery::class)->discover(),
            );
        }

        $configured = $config->get('atrium.plugins', []);

        if (is_array($configured)) {
            $registry->registerMany($configured);
        }
    }

    /**
     * Plugin routes go in their own group with the dashboard's attributes,
     * registered once every provider has booted. That ordering lets a host
     * application register a plugin in its own boot() and still get its
     * routes, while the shared attributes keep those routes behind Atrium's
     * prefix and middleware.
     */
    private function bootRoutes(): void
    {
        if ($this->routesAreCached()) {
            return;
        }

        $attributes = $this->dashboardRouteAttributes();

        $this->app->booted(function () use ($attributes): void {
            Route::group($attributes, __DIR__.'/routes.php');
        });
    }
}
