<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Themes\Services;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use JayI\Atrium\Domains\Access\Services\Gatekeeper;
use JayI\Atrium\Domains\Themes\Data\Theme;
use Throwable;

/**
 * The dashboard's themes, which one shows by default, and whether people may
 * switch between them.
 */
class ThemeRegistry
{
    /** @var array<string, Theme> */
    private array $themes = [];

    public function __construct(
        private readonly Repository $config,
        private readonly Gatekeeper $gatekeeper,
    ) {}

    /**
     * Add a theme, or replace the one with the same key.
     */
    public function register(Theme $theme): static
    {
        $this->themes[$theme->key] = $theme;

        return $this;
    }

    /**
     * @return array<string, Theme>
     */
    public function all(): array
    {
        return $this->themes;
    }

    /**
     * Each theme's layout, by key, for the script that applies a theme
     * before the page paints.
     *
     * @return array<string, string>
     */
    public function layouts(): array
    {
        return array_map(fn (Theme $theme): string => $theme->layout, $this->themes);
    }

    public function find(string $key): ?Theme
    {
        return $this->themes[$key] ?? null;
    }

    /**
     * The theme shown until someone picks another: `atrium.themes.default`,
     * or the first registered when that names no theme.
     */
    public function default(): ?Theme
    {
        $key = $this->config->get('atrium.themes.default');

        return (is_string($key) ? $this->find($key) : null) ?? (array_values($this->themes)[0] ?? null);
    }

    /**
     * Whether the theme switcher shows: there is more than one theme, and
     * the feature `atrium.themes.switcher_feature` names is on. A feature
     * class whose package is missing gates nothing, as plugin features do.
     */
    public function switchable(Request $request): bool
    {
        if (count($this->themes) < 2) {
            return false;
        }

        $feature = $this->config->get('atrium.themes.switcher_feature');

        if (! is_string($feature) || $feature === '' || (str_contains($feature, '\\') && ! self::loads($feature))) {
            return true;
        }

        return $this->gatekeeper->enabled($feature, $request);
    }

    private static function loads(string $class): bool
    {
        try {
            return class_exists($class);
        } catch (Throwable) {
            return false;
        }
    }
}
