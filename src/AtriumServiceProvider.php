<?php

declare(strict_types=1);

namespace JayI\Atrium;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use JayI\Atrium\Console\Commands\InstallCommand;
use JayI\Atrium\Domains\DomainServiceProvider;
use JayI\Atrium\Support\StyleRegistry;

class AtriumServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/atrium.php', 'atrium');

        $this->app->singleton(StyleRegistry::class);
        $this->app->singleton(Atrium::class);

        $this->app->register(DomainServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

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
}
