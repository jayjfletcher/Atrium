<?php

declare(strict_types=1);

namespace JayI\Atrium\Support;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Base class for Atrium's domain service providers.
 *
 * Every dashboard route shares one group - the configured path, domain and
 * middleware, and the `atrium.` name prefix - so each domain loads its
 * routes file through `loadDashboardRoutesFrom()` rather than building the
 * group itself.
 */
abstract class ServiceProvider extends BaseServiceProvider
{
    /**
     * Load a routes file inside the dashboard's route group.
     */
    protected function loadDashboardRoutesFrom(string $path): void
    {
        if ($this->routesAreCached()) {
            return;
        }

        Route::group($this->dashboardRouteAttributes(), $path);
    }

    /**
     * The attributes every dashboard route is registered with.
     *
     * @return array<string, mixed>
     */
    protected function dashboardRouteAttributes(): array
    {
        $config = $this->app->make(Repository::class);

        return array_filter([
            'domain' => $config->get('atrium.domain'),
            'prefix' => $config->get('atrium.path', 'atrium'),
            'middleware' => $config->get('atrium.middleware', ['web']),
            'as' => 'atrium.',
        ]);
    }

    protected function routesAreCached(): bool
    {
        return $this->app instanceof CachesRoutes && $this->app->routesAreCached();
    }
}
