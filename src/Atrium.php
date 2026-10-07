<?php

declare(strict_types=1);

namespace JayI\Atrium;

use Closure;
use Illuminate\Http\Request;
use JayI\Atrium\Domains\Access\Services\Gatekeeper;
use JayI\Atrium\Domains\Navigation\Data\NavGroup;
use JayI\Atrium\Domains\Navigation\Data\NavItem;
use JayI\Atrium\Domains\Navigation\Services\NavigationRegistry;
use JayI\Atrium\Domains\Plugins\Contracts\Plugin;
use JayI\Atrium\Domains\Plugins\Services\PluginRegistry;
use JayI\Atrium\Domains\Search\Data\SearchResult;
use JayI\Atrium\Domains\Search\Data\SearchSource;
use JayI\Atrium\Domains\Search\Services\SearchRegistry;
use JayI\Atrium\Domains\Settings\Data\SettingsPanel;
use JayI\Atrium\Domains\Settings\Services\SettingsRegistry;
use JayI\Atrium\Domains\Themes\Data\Theme;
use JayI\Atrium\Domains\Themes\Services\ThemeRegistry;
use JayI\Atrium\Domains\Widgets\Data\WidgetDefinition;
use JayI\Atrium\Domains\Widgets\Services\WidgetRegistry;
use JayI\Atrium\Support\StyleRegistry;

class Atrium
{
    public function __construct(
        protected PluginRegistry $plugins,
        protected NavigationRegistry $navigation,
        protected WidgetRegistry $widgets,
        protected SettingsRegistry $settings,
        protected SearchRegistry $search,
        protected Gatekeeper $gatekeeper,
        protected StyleRegistry $styles,
        protected ThemeRegistry $themes,
    ) {}

    /**
     * Offer a theme in the dashboard's theme switcher, or replace the one
     * with the same key.
     */
    public function theme(Theme $theme): static
    {
        $this->themes->register($theme);

        return $this;
    }

    /**
     * @return array<string, Theme>
     */
    public function themes(): array
    {
        return $this->themes->all();
    }

    /**
     * Link a stylesheet in the dashboard's <head>, after Atrium's own.
     */
    public function stylesheet(string $href): static
    {
        $this->styles->stylesheet($href);

        return $this;
    }

    /**
     * Add CSS to the dashboard's <head>, after Atrium's own stylesheet - for
     * utilities a package uses that Atrium's precompiled stylesheet lacks.
     */
    public function css(string $css, ?string $key = null): static
    {
        $this->styles->css($css, $key);

        return $this;
    }

    /**
     * Register a plugin with Atrium.
     *
     * @param  Plugin|class-string  $plugin
     */
    public function plugin(Plugin|string $plugin): static
    {
        $this->plugins->register($plugin);

        return $this;
    }

    /**
     * @return array<string, Plugin>
     */
    public function plugins(): array
    {
        return $this->plugins->all();
    }

    public function pluginRegistry(): PluginRegistry
    {
        return $this->plugins;
    }

    /**
     * Add a navigation item outside of any plugin.
     */
    public function nav(NavItem $item): static
    {
        $this->navigation->add($item);

        return $this;
    }

    /**
     * Describe a sidebar group's visibility outside of any plugin.
     */
    public function navigationGroup(NavGroup $group): static
    {
        $this->navigation->group($group);

        return $this;
    }

    /**
     * Decide the permissions `can()` checks with the given callback instead
     * of the Gate.
     *
     * @param  (Closure(string $ability, array<array-key, mixed> $arguments, Request $request): bool)|null  $callback
     */
    public function resolvePermissionsUsing(?Closure $callback): static
    {
        $this->gatekeeper->resolvePermissionsUsing($callback);

        return $this;
    }

    /**
     * Decide whether the features `feature()` and plugins name are on. Until
     * a resolver is registered every feature is on.
     *
     * @param  (Closure(string $feature, Request $request): bool)|null  $callback
     */
    public function resolveFeaturesUsing(?Closure $callback): static
    {
        $this->gatekeeper->resolveFeaturesUsing($callback);

        return $this;
    }

    public function featureEnabled(string $feature, ?Request $request = null): bool
    {
        return $this->gatekeeper->enabled($feature, $request ?? request());
    }

    /**
     * @return array<int, NavItem>
     */
    public function navigation(?Request $request = null): array
    {
        return $this->navigation->items($request ?? request());
    }

    /**
     * @return array<string, array<int, NavItem>>
     */
    public function navigationGroups(?Request $request = null): array
    {
        return $this->navigation->grouped($request ?? request());
    }

    /**
     * Make a widget type available outside of any plugin.
     */
    public function widget(WidgetDefinition $definition): static
    {
        $this->widgets->add($definition);

        return $this;
    }

    /**
     * Widget types available to place. Never placed automatically.
     *
     * @return array<string, WidgetDefinition>
     */
    public function widgets(?Request $request = null): array
    {
        return $this->widgets->available($request ?? request());
    }

    public function widgetRegistry(): WidgetRegistry
    {
        return $this->widgets;
    }

    public function settingsPanel(SettingsPanel $panel): static
    {
        $this->settings->add($panel);

        return $this;
    }

    /**
     * @return array<int, SettingsPanel>
     */
    public function settings(?Request $request = null): array
    {
        return $this->settings->panels($request ?? request());
    }

    public function searchSource(SearchSource $source): static
    {
        $this->search->add($source);

        return $this;
    }

    /**
     * @return array<int, SearchResult>
     */
    public function search(string $query, ?Request $request = null): array
    {
        return $this->search->search($request ?? request(), $query);
    }

    /**
     * The configured dashboard URL path.
     */
    public function path(): string
    {
        $path = config('atrium.path');

        return is_string($path) ? trim($path, '/') : 'atrium';
    }

    /**
     * A URL within the dashboard.
     */
    public function url(string $path = ''): string
    {
        return url(trim($this->path().'/'.ltrim($path, '/'), '/'));
    }
}
