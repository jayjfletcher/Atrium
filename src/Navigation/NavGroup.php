<?php

declare(strict_types=1);

namespace JayI\Atrium\Navigation;

use JayI\Atrium\Access\Concerns\Gated;

/**
 * Visibility rules for a whole sidebar section. Items join a group by
 * naming it in NavItem::group(); the group hides them all when its rules
 * fail. A group nobody describes is always shown while it has items.
 */
class NavGroup
{
    use Gated;

    final public function __construct(public readonly string $name) {}

    public static function make(string $name): static
    {
        return new static($name);
    }
}
