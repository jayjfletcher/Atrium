<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Widgets;

use RefactorCircus\Atrium\Domains\Widgets\Services\WidgetRegistry;
use RefactorCircus\Atrium\Support\ServiceProvider;

class WidgetsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WidgetRegistry::class);
    }
}
