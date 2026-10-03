<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Search;

use JayI\Atrium\Domains\Search\Services\SearchRegistry;
use JayI\Atrium\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SearchRegistry::class);
    }

    public function boot(): void
    {
        $this->loadDashboardRoutesFrom(__DIR__.'/routes.php');
    }
}
