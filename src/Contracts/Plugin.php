<?php

declare(strict_types=1);

namespace Atrium\Atrium\Contracts;

use Atrium\Atrium\Navigation\NavItem;
use Atrium\Atrium\Search\SearchSource;
use Atrium\Atrium\Settings\SettingsPanel;
use Atrium\Atrium\Widgets\WidgetDefinition;
use Illuminate\Http\Request;

interface Plugin
{
    /**
     * Stable identifier used in config, caching, and widget keys.
     */
    public function key(): string;

    /**
     * Human readable name shown in the dashboard.
     */
    public function label(): string;

    /**
     * Determine whether the current request may see this plugin at all.
     */
    public function authorize(Request $request): bool;

    /**
     * Navigation entries contributed to the dashboard sidebar.
     *
     * @return array<int, NavItem>
     */
    public function navigation(): array;

    /**
     * Register routes. Called inside Atrium's route group, so the prefix,
     * middleware, and name prefix are already applied.
     */
    public function routes(): void;

    /**
     * Settings section contributed to the dashboard settings page.
     */
    public function settings(): ?SettingsPanel;

    /**
     * Widget types this plugin makes available.
     *
     * These are offered in the widget picker, never placed on a dashboard
     * automatically. Placement is always a user action.
     *
     * @return array<int, WidgetDefinition>
     */
    public function widgets(): array;

    /**
     * Source queried by the global search palette.
     */
    public function search(): ?SearchSource;
}
