<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Dashboard;

use Illuminate\Support\Facades\Route;
use RefactorCircus\Atrium\Domains\Dashboard\Models\DashboardModel;
use RefactorCircus\Atrium\Domains\Dashboard\Services\DashboardManager;
use RefactorCircus\Atrium\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DashboardManager::class);
    }

    public function boot(): void
    {
        $this->bootRoutes();
    }

    private function bootRoutes(): void
    {
        Route::model('dashboard', DashboardModel::class);

        $this->loadDashboardRoutesFrom(__DIR__.'/routes.php');
    }
}
