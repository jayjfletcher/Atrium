<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Dashboard;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Route;
use JayI\Atrium\Domains\Dashboard\Models\DashboardModel;
use JayI\Atrium\Domains\Dashboard\Models\DashboardWidgetModel;
use JayI\Atrium\Domains\Dashboard\Services\DashboardManager;
use JayI\Atrium\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DashboardManager::class);
    }

    public function boot(): void
    {
        $this->bootMorphMap();

        $this->bootRoutes();
    }

    /**
     * Keep the class names these models were stored under before they moved
     * into this domain, so any polymorphic `*_type` column, audit trail or
     * other record an application wrote with the old names still resolves -
     * and new records keep writing the same value.
     */
    private function bootMorphMap(): void
    {
        Relation::morphMap([
            'JayI\Atrium\Models\Dashboard' => DashboardModel::class,
            'JayI\Atrium\Models\DashboardWidget' => DashboardWidgetModel::class,
        ]);
    }

    private function bootRoutes(): void
    {
        Route::model('dashboard', DashboardModel::class);

        $this->loadDashboardRoutesFrom(__DIR__.'/routes.php');
    }
}
