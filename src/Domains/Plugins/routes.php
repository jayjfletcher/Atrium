<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use RefactorCircus\Atrium\Domains\Access\Http\Middleware\EnsureFeaturesAreEnabled;
use RefactorCircus\Atrium\Domains\Plugins\Services\PluginRegistry;

foreach (app(PluginRegistry::class)->all() as $plugin) {
    // A plugin's routes go away with its features, as its links do.
    $features = $plugin->features();

    $features === []
        ? $plugin->routes()
        : Route::middleware(EnsureFeaturesAreEnabled::class.':'.implode(',', $features))->group(fn () => $plugin->routes());
}
