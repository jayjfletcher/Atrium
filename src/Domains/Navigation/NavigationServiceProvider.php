<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Navigation;

use JayI\Atrium\Domains\Navigation\Services\NavigationRegistry;
use JayI\Atrium\Support\ServiceProvider;

class NavigationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NavigationRegistry::class);
    }
}
