<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Navigation\Data;

use JayI\Atrium\Domains\Access\Concerns\Gated;

/**
 * A sidebar section: its icon in the rail, its place there, and visibility
 * rules for all of its pages. Items join a group by naming it in
 * NavItem::group(); the group hides them all when its rules fail. A group
 * nobody describes is always shown while it has items, with its first
 * page's icon.
 */
class NavGroup
{
    use Gated;

    /** The section's icon in the sidebar rail, as an SVG string. */
    public private(set) ?string $icon = null;

    /** Where the section sits in the rail; lower first. */
    public private(set) ?int $sort = null;

    final public function __construct(public readonly string $name) {}

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function sort(int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }
}
