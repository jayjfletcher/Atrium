<?php

declare(strict_types=1);

namespace JayI\Atrium;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use JayI\Atrium\Console\Commands\InstallCommand;
use JayI\Atrium\Console\Commands\MakePluginCommand;
use JayI\Atrium\Console\Commands\PluginListCommand;
use JayI\Atrium\Dashboards\DashboardManager;
use JayI\Atrium\Models\Dashboard;
use JayI\Atrium\Navigation\NavigationRegistry;
use JayI\Atrium\Plugins\PluginRegistry;
use JayI\Atrium\Search\SearchRegistry;
use JayI\Atrium\Settings\SettingsRegistry;
use JayI\Atrium\Support\Discovery\ComposerPluginDiscovery;
use JayI\Atrium\Widgets\WidgetRegistry;

class AtriumServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/atrium.php', 'atrium');

        $this->app->singleton(ComposerPluginDiscovery::class, fn (Application $app): ComposerPluginDiscovery => new ComposerPluginDiscovery(
            $app->make(Filesystem::class),
            $app->basePath('vendor'),
        ));

        $this->app->singleton(PluginRegistry::class, function (Application $app): PluginRegistry {
            $disabled = $app->make(Repository::class)->get('atrium.disabled', []);

            return new PluginRegistry($app)->disable(is_array($disabled) ? $disabled : []);
        });

        $this->app->singleton(NavigationRegistry::class);
        $this->app->singleton(WidgetRegistry::class);
        $this->app->singleton(SettingsRegistry::class);
        $this->app->singleton(SearchRegistry::class);
        $this->app->singleton(DashboardManager::class);

        $this->app->singleton(Atrium::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPlugins();

        $this->registerPolicies();

        Route::model('dashboard', Dashboard::class);

        $this->loadRoutesFrom(__DIR__.'/../routes/atrium.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'atrium');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'atrium');

        // Exposes the shared library as <x-atrium::card>, usable anywhere in
        // the host application, inside the dashboard shell or outside it.
        Blade::anonymousComponentNamespace('atrium::components', 'atrium');

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/atrium.php' => config_path('atrium.php'),
        ], ['atrium', 'atrium-config']);

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/atrium'),
        ], ['atrium', 'atrium-views']);

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/atrium'),
        ], ['atrium', 'atrium-lang']);

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/atrium'),
        ], ['atrium', 'atrium-assets']);

        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], ['atrium', 'atrium-migrations']);

        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs/atrium'),
        ], ['atrium', 'atrium-stubs']);

        $this->commands([
            InstallCommand::class,
            MakePluginCommand::class,
            PluginListCommand::class,
        ]);
    }

    /**
     * Register the model policies from `atrium.policies` with the Gate.
     */
    protected function registerPolicies(): void
    {
        /** @var array<class-string, class-string> $policies */
        $policies = $this->app->make(Repository::class)->get('atrium.policies', []);

        foreach ($policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    /**
     * Register discovered and configured plugins.
     */
    protected function registerPlugins(): void
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
}
