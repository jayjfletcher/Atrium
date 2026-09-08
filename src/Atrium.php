<?php

declare(strict_types=1);

namespace Atrium\Atrium;

use Atrium\Atrium\Contracts\Plugin;
use Atrium\Atrium\Navigation\NavigationRegistry;
use Atrium\Atrium\Navigation\NavItem;
use Atrium\Atrium\Plugins\PluginRegistry;
use Atrium\Atrium\Search\SearchRegistry;
use Atrium\Atrium\Search\SearchResult;
use Atrium\Atrium\Search\SearchSource;
use Atrium\Atrium\Settings\SettingsPanel;
use Atrium\Atrium\Settings\SettingsRegistry;
use Atrium\Atrium\Widgets\WidgetDefinition;
use Atrium\Atrium\Widgets\WidgetRegistry;
use Illuminate\Http\Request;

class Atrium
{
    public function __construct(
        protected PluginRegistry $plugins,
        protected NavigationRegistry $navigation,
        protected WidgetRegistry $widgets,
        protected SettingsRegistry $settings,
        protected SearchRegistry $search,
    ) {}

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
