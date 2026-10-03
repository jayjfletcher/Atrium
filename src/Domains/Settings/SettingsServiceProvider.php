<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Settings;

use JayI\Atrium\Domains\Settings\Services\SettingsRegistry;
use JayI\Atrium\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsRegistry::class);
    }

    public function boot(): void
    {
        $this->loadDashboardRoutesFrom(__DIR__.'/routes.php');
    }
}
