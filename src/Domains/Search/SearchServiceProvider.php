<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Search;

use RefactorCircus\Atrium\Domains\Search\Services\SearchRegistry;
use RefactorCircus\Atrium\Support\ServiceProvider;

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
