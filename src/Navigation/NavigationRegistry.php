<?php

declare(strict_types=1);

namespace Atrium\Atrium\Navigation;

use Atrium\Atrium\Contracts\Plugin;
use Atrium\Atrium\Plugins\PluginRegistry;
use Illuminate\Http\Request;

class NavigationRegistry
{
    /** @var array<int, NavItem> */
    protected array $items = [];

    public function __construct(protected PluginRegistry $plugins) {}

    /**
     * Add a navigation item outside of any plugin, e.g. from the host app.
     */
    public function add(NavItem $item): static
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Navigation items visible to the given request, grouped and sorted.
     *
     * @return array<string, array<int, NavItem>>
     */
    public function grouped(Request $request): array
    {
        $groups = [];

        foreach ($this->items($request) as $item) {
            $groups[$item->group ?? ''][] = $item;
        }

        foreach ($groups as $group => $items) {
            usort($items, fn (NavItem $a, NavItem $b): int => $a->sort <=> $b->sort
                ?: strcmp($a->label, $b->label));

            $groups[$group] = $items;
        }

        return $groups;
    }

    /**
     * Flat list of authorized navigation items, sorted.
     *
     * @return array<int, NavItem>
     */
    public function items(Request $request): array
    {
        $items = $this->items;

        foreach ($this->plugins->authorized($request) as $plugin) {
            foreach ($this->itemsFor($plugin) as $item) {
                $items[] = $item;
            }
        }

        $items = array_values(array_filter(
            $items,
            fn (NavItem $item): bool => $item->isAuthorized($request),
        ));

        usort($items, fn (NavItem $a, NavItem $b): int => $a->sort <=> $b->sort
            ?: strcmp($a->label, $b->label));

        return $items;
    }

    /**
     * @return array<int, NavItem>
     */
    protected function itemsFor(Plugin $plugin): array
    {
        return $plugin->navigation();
    }
}
