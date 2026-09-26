<?php

declare(strict_types=1);

namespace JayI\Atrium\Plugins;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use JayI\Atrium\Http\Controllers\FeatureFlagController;
use JayI\Atrium\Navigation\NavItem;

/**
 * Manages the feature flag values Pennant has stored.
 *
 * Registered automatically when laravel/pennant is installed and
 * `atrium.pennant.enabled` is true. Hide it like any other plugin by listing
 * its `pennant` key under `atrium.disabled`.
 */
class PennantPlugin extends Plugin
{
    public function key(): string
    {
        return 'pennant';
    }

    public function label(): string
    {
        return __('atrium::atrium.pennant_features');
    }

    /**
     * Checks `atrium.pennant.gate` when one is configured, on top of the
     * dashboard gate every Atrium route already enforces.
     */
    public function authorize(Request $request): bool
    {
        $ability = config('atrium.pennant.gate');

        if (! is_string($ability) || $ability === '') {
            return true;
        }

        return Gate::forUser($request->user())->allows($ability, [$request]);
    }

    public function navigation(): array
    {
        return [
            NavItem::make(__('atrium::atrium.pennant_features'))
                ->route('atrium.pennant.index')
                ->group(__('atrium::atrium.pennant_group'))
                ->sort(900),
        ];
    }

    public function routes(): void
    {
        Route::get('pennant', [FeatureFlagController::class, 'index'])->name('pennant.index');
        Route::get('pennant/scopes', [FeatureFlagController::class, 'scopes'])->name('pennant.scopes');
        Route::put('pennant/values', [FeatureFlagController::class, 'update'])->name('pennant.values.update');
        Route::delete('pennant/values', [FeatureFlagController::class, 'destroy'])->name('pennant.values.destroy');
        Route::delete('pennant/features', [FeatureFlagController::class, 'purge'])->name('pennant.features.purge');
    }
}
