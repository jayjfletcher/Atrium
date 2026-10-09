<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Themes;

use Illuminate\Contracts\Config\Repository;
use RefactorCircus\Atrium\Domains\Themes\Data\Theme;
use RefactorCircus\Atrium\Domains\Themes\Services\ThemeRegistry;
use RefactorCircus\Atrium\Support\ServiceProvider;

/**
 * Atrium's themes: the built-in six, those from `atrium.themes.available`,
 * and any a package registers with `Atrium::theme()`.
 */
class ThemesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeRegistry::class);

        $this->app->afterResolving(ThemeRegistry::class, function (ThemeRegistry $themes): void {
            $config = $this->app->make(Repository::class);

            // The compiled look, retuned by the long-standing `atrium.theme`.
            /** @var array<string, string> $overrides */
            $overrides = array_filter((array) $config->get('atrium.theme', []), is_scalar(...));

            $themes->register(Theme::make('atrium')
                ->label(__('atrium::atrium.theme_atrium'))
                ->swatch('#4f46e5')
                ->colors(array_map(strval(...), $overrides)));

            $themes->register(self::harbor());
            $themes->register(self::sunset());
            $themes->register(self::forest());
            $themes->register(self::midnight());
            $themes->register(self::ledger());

            /** @var array<string, array<string, mixed>> $available */
            $available = (array) $config->get('atrium.themes.available', []);

            foreach ($available as $key => $definition) {
                $themes->register(Theme::make((string) $key)
                    ->label(is_string($definition['label'] ?? null) ? $definition['label'] : ucfirst((string) $key))
                    ->colors(array_map(strval(...), array_filter((array) ($definition['colors'] ?? []), is_scalar(...))))
                    ->radius(is_string($definition['radius'] ?? null) ? $definition['radius'] : null)
                    ->swatch(is_string($definition['swatch'] ?? null) ? $definition['swatch'] : null)
                    ->layout(is_string($definition['layout'] ?? null) ? $definition['layout'] : Theme::SIDEBAR));
            }
        });
    }

    /**
     * Teal on slate, with softer corners.
     */
    public static function harbor(): Theme
    {
        return Theme::make('harbor')
            ->label(__('atrium::atrium.theme_harbor'))
            ->swatch('#0d9488')
            ->radius('0.75rem')
            ->colors([
                'canvas' => '#f1f5f9',
                'surface' => '#ffffff',
                'surface-alt' => '#f8fafc',
                'on-surface' => '#475569',
                'on-surface-strong' => '#0f172a',
                'primary' => '#0d9488',
                'on-primary' => '#ffffff',
                'secondary' => '#0f172a',
                'on-secondary' => '#ffffff',
                'outline' => '#e2e8f0',
                'outline-strong' => '#1e293b',

                'canvas-dark' => '#020617',
                'surface-dark' => '#0f172a',
                'surface-dark-alt' => '#1e293b',
                'on-surface-dark' => '#94a3b8',
                'on-surface-dark-strong' => '#f8fafc',
                'primary-dark' => '#2dd4bf',
                'on-primary-dark' => '#042f2e',
                'secondary-dark' => '#f1f5f9',
                'on-secondary-dark' => '#020617',
                'outline-dark' => '#1e293b',
                'outline-dark-strong' => '#cbd5e1',

                'info' => '#0284c7',
                'success' => '#059669',
                'warning' => '#d97706',
                'danger' => '#e11d48',
            ]);
    }

    /**
     * Orange on warm stone.
     */
    public static function sunset(): Theme
    {
        return Theme::make('sunset')
            ->label(__('atrium::atrium.theme_sunset'))
            ->swatch('#ea580c')
            ->radius('0.625rem')
            ->colors([
                'canvas' => '#f5f5f4',
                'surface' => '#ffffff',
                'surface-alt' => '#fafaf9',
                'on-surface' => '#57534e',
                'on-surface-strong' => '#1c1917',
                'primary' => '#ea580c',
                'on-primary' => '#ffffff',
                'secondary' => '#1c1917',
                'on-secondary' => '#ffffff',
                'outline' => '#e7e5e4',
                'outline-strong' => '#292524',

                'canvas-dark' => '#0c0a09',
                'surface-dark' => '#1c1917',
                'surface-dark-alt' => '#292524',
                'on-surface-dark' => '#a8a29e',
                'on-surface-dark-strong' => '#fafaf9',
                'primary-dark' => '#fb923c',
                'on-primary-dark' => '#1c1917',
                'secondary-dark' => '#f5f5f4',
                'on-secondary-dark' => '#0c0a09',
                'outline-dark' => '#292524',
                'outline-dark-strong' => '#d6d3d1',
            ]);
    }

    /**
     * Green on sage.
     */
    public static function forest(): Theme
    {
        return Theme::make('forest')
            ->label(__('atrium::atrium.theme_forest'))
            ->swatch('#15803d')
            ->radius('0.5rem')
            ->colors([
                'canvas' => '#f1f4f1',
                'surface' => '#ffffff',
                'surface-alt' => '#f8faf8',
                'on-surface' => '#4d5b52',
                'on-surface-strong' => '#14231a',
                'primary' => '#15803d',
                'on-primary' => '#ffffff',
                'secondary' => '#14231a',
                'on-secondary' => '#ffffff',
                'outline' => '#dfe5df',
                'outline-strong' => '#22332a',

                'canvas-dark' => '#0a110d',
                'surface-dark' => '#111b15',
                'surface-dark-alt' => '#1a2820',
                'on-surface-dark' => '#9fb3a6',
                'on-surface-dark-strong' => '#eef5f0',
                'primary-dark' => '#4ade80',
                'on-primary-dark' => '#052e16',
                'secondary-dark' => '#eef5f0',
                'on-secondary-dark' => '#0a110d',
                'outline-dark' => '#1f3127',
                'outline-dark-strong' => '#c5d4ca',

                'success' => '#15803d',
            ]);
    }

    /**
     * Violet on deep indigo, with the roundest corners.
     */
    public static function midnight(): Theme
    {
        return Theme::make('midnight')
            ->label(__('atrium::atrium.theme_midnight'))
            ->swatch('#7c3aed')
            ->radius('0.875rem')
            ->colors([
                'canvas' => '#eef0f8',
                'surface' => '#ffffff',
                'surface-alt' => '#f7f8fc',
                'on-surface' => '#4b5070',
                'on-surface-strong' => '#151833',
                'primary' => '#7c3aed',
                'on-primary' => '#ffffff',
                'secondary' => '#151833',
                'on-secondary' => '#ffffff',
                'outline' => '#dfe2f0',
                'outline-strong' => '#23284a',

                'canvas-dark' => '#070815',
                'surface-dark' => '#0f1124',
                'surface-dark-alt' => '#1a1d38',
                'on-surface-dark' => '#9ca0c4',
                'on-surface-dark-strong' => '#f1f2fb',
                'primary-dark' => '#a78bfa',
                'on-primary-dark' => '#1e0b4b',
                'secondary-dark' => '#f1f2fb',
                'on-secondary-dark' => '#070815',
                'outline-dark' => '#22264a',
                'outline-dark-strong' => '#c8cbe6',
            ]);
    }

    /**
     * Monochrome with near-square corners, and navigation across the top.
     */
    public static function ledger(): Theme
    {
        return Theme::make('ledger')
            ->label(__('atrium::atrium.theme_ledger'))
            // Mid grey, so the dot still shows in dark mode.
            ->swatch('#404040')
            ->radius('0.25rem')
            ->layout(Theme::TOP)
            ->colors([
                'canvas' => '#fafafa',
                'surface' => '#ffffff',
                'surface-alt' => '#f5f5f5',
                'on-surface' => '#525252',
                'on-surface-strong' => '#0a0a0a',
                'primary' => '#171717',
                'on-primary' => '#ffffff',
                'secondary' => '#0a0a0a',
                'on-secondary' => '#ffffff',
                'outline' => '#e5e5e5',
                'outline-strong' => '#262626',

                'canvas-dark' => '#0a0a0a',
                'surface-dark' => '#141414',
                'surface-dark-alt' => '#1f1f1f',
                'on-surface-dark' => '#a3a3a3',
                'on-surface-dark-strong' => '#fafafa',
                'primary-dark' => '#fafafa',
                'on-primary-dark' => '#0a0a0a',
                'secondary-dark' => '#fafafa',
                'on-secondary-dark' => '#0a0a0a',
                'outline-dark' => '#262626',
                'outline-dark-strong' => '#d4d4d4',
            ]);
    }
}
