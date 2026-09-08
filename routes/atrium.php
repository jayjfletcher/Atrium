<?php

declare(strict_types=1);

use Atrium\Atrium\Http\Controllers\DashboardController;
use Atrium\Atrium\Http\Controllers\DashboardCrudController;
use Atrium\Atrium\Http\Controllers\DashboardLayoutController;
use Atrium\Atrium\Http\Controllers\SearchController;
use Atrium\Atrium\Http\Controllers\SettingsController;
use Atrium\Atrium\Plugins\PluginRegistry;
use Illuminate\Support\Facades\Route;

$config = app('config');

$attributes = array_filter([
    'domain' => $config->get('atrium.domain'),
    'prefix' => $config->get('atrium.path', 'atrium'),
    'middleware' => $config->get('atrium.middleware', ['web']),
    'as' => 'atrium.',
]);

Route::group($attributes, function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('dashboards', [DashboardCrudController::class, 'store'])->name('dashboards.store');
    Route::put('dashboards/{dashboard}', [DashboardCrudController::class, 'update'])->name('dashboards.update');
    Route::delete('dashboards/{dashboard}', [DashboardCrudController::class, 'destroy'])->name('dashboards.destroy');
    Route::put('dashboards/{dashboard}/layout', [DashboardLayoutController::class, 'update'])->name('dashboards.layout');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    Route::get('settings/{panel}', [SettingsController::class, 'show'])->name('settings.show');

    Route::get('search', SearchController::class)->name('search');

    // Declared last so its wildcard cannot shadow the routes above.
    Route::get('d/{dashboard}', [DashboardController::class, 'show'])->name('dashboard.show');
});

// Plugin routes go in a second group with the same attributes, registered
// once every provider has booted. That ordering lets a host application
// register a plugin in its own boot() and still get its routes, while the
// shared attributes keep those routes behind Atrium's prefix and middleware.
app()->booted(function () use ($attributes): void {
    Route::group($attributes, function (): void {
        foreach (app(PluginRegistry::class)->all() as $plugin) {
            $plugin->routes();
        }
    });
});
