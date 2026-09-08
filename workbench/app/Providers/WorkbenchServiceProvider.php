<?php

namespace Workbench\App\Providers;

use Atrium\Atrium\Facades\Atrium;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Workbench\App\Atrium\DemoPlugin;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'workbench');

        // Installed packages are discovered from their composer.json; the
        // workbench app is the host, so it registers its plugin explicitly.
        Atrium::plugin(DemoPlugin::class);

        // The workbench dashboard is open so `composer serve` is usable
        // without logging in. A real application defines a real gate.
        Gate::define('viewAtrium', fn ($user = null): bool => true);
    }
}
