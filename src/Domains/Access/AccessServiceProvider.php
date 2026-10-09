<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Access;

use Illuminate\Support\Facades\Route;
use RefactorCircus\Atrium\Domains\Access\Http\Middleware\EnsureFeaturesAreEnabled;
use RefactorCircus\Atrium\Domains\Access\Services\Gatekeeper;
use RefactorCircus\Atrium\Support\ServiceProvider;

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
