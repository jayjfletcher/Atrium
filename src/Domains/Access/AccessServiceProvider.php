<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Access;

use Illuminate\Support\Facades\Route;
use JayI\Atrium\Domains\Access\Http\Middleware\EnsureFeaturesAreEnabled;
use JayI\Atrium\Domains\Access\Services\Gatekeeper;
use JayI\Atrium\Support\ServiceProvider;

class AccessServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Gatekeeper::class);
    }

    public function boot(): void
    {
        Route::aliasMiddleware('atrium.feature', EnsureFeaturesAreEnabled::class);
    }
}
