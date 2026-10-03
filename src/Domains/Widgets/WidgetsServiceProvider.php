<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Widgets;

use JayI\Atrium\Domains\Widgets\Services\WidgetRegistry;
use JayI\Atrium\Support\ServiceProvider;

class WidgetsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WidgetRegistry::class);
    }
}
