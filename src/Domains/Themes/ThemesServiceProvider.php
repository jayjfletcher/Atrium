<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Themes;

use Illuminate\Contracts\Config\Repository;
use JayI\Atrium\Domains\Themes\Data\Theme;
use JayI\Atrium\Domains\Themes\Services\ThemeRegistry;
use JayI\Atrium\Support\ServiceProvider;

/**
 * Atrium's themes: the built-in two, those from `atrium.themes.available`,
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

            /** @var array<string, array<string, mixed>> $available */
            $available = (array) $config->get('atrium.themes.available', []);

            foreach ($available as $key => $definition) {
                $themes->register(Theme::make((string) $key)
                    ->label(is_string($definition['label'] ?? null) ? $definition['label'] : ucfirst((string) $key))
                    ->colors(array_map(strval(...), array_filter((array) ($definition['colors'] ?? []), is_scalar(...))))
                    ->radius(is_string($definition['radius'] ?? null) ? $definition['radius'] : null)
                    ->swatch(is_string($definition['swatch'] ?? null) ? $definition['swatch'] : null));
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
}
