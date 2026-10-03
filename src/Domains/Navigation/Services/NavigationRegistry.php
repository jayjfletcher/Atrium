<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Navigation\Services;

use Illuminate\Http\Request;
use JayI\Atrium\Domains\Navigation\Data\NavGroup;
use JayI\Atrium\Domains\Navigation\Data\NavItem;
use JayI\Atrium\Domains\Plugins\Contracts\Plugin;
use JayI\Atrium\Domains\Plugins\Services\PluginRegistry;

class NavigationRegistry
{
    /** @var array<int, NavItem> */
    protected array $items = [];

    /** @var array<string, NavGroup> */
    protected array $groups = [];

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
     * Describe a sidebar group outside of any plugin. Describing a group
     * twice keeps the later description.
     */
    public function group(NavGroup $group): static
    {
        $this->groups[$group->name] = $group;

        return $this;
    }

    /**
     * Navigation items visible to the given request, grouped and sorted.
     * A group whose own rules fail is left out with all of its items.
     *
     * @return array<string, array<int, NavItem>>
     */
    public function grouped(Request $request): array
    {
        $described = $this->groups($request);
        $groups = [];

        foreach ($this->items($request) as $item) {
            $name = $item->group ?? '';

            if (isset($described[$name]) && ! $described[$name]->isAuthorized($request)) {
                continue;
            }

            $groups[$name][] = $item;
        }

        return $groups;
    }

    /**
     * Flat list of visible navigation items, sorted. Children the request may
     * not see are removed, and a parent left with no children and no link of
     * its own goes with them.
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

        $items = $this->visible($items, $request);

        usort($items, fn (NavItem $a, NavItem $b): int => $a->sort <=> $b->sort
            ?: strcmp($a->label, $b->label));

        return $items;
    }

    /**
     * Every described group, including those plugins describe, keyed by name.
     *
     * @return array<string, NavGroup>
     */
    public function groups(Request $request): array
    {
        $groups = [];

        foreach ($this->plugins->authorized($request) as $plugin) {
            foreach ($plugin->navigationGroups() as $group) {
                $groups[$group->name] = $group;
            }
        }

        // Groups described directly, typically by the host application,
        // win over a plugin's description of the same group.
        return [...$groups, ...$this->groups];
    }

    /**
     * @param  array<int, NavItem>  $items
     * @return array<int, NavItem>
     */
    protected function visible(array $items, Request $request): array
    {
        $visible = [];

        foreach ($items as $item) {
            if (! $item->isAuthorized($request)) {
                continue;
            }

            if ($item->children !== []) {
                $children = $this->visible($item->children, $request);

                if ($children === [] && $item->resolveUrl() === null) {
                    continue;
                }

                // Registered items are shared across requests, so filter a copy.
                $item = (clone $item)->children($children);
            }

            $visible[] = $item;
        }

        return $visible;
    }

    /**
     * @return array<int, NavItem>
     */
    protected function itemsFor(Plugin $plugin): array
    {
        return $plugin->navigation();
    }
}
