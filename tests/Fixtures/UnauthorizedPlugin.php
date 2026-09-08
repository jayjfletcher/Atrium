<?php

declare(strict_types=1);

namespace Atrium\Atrium\Tests\Fixtures;

use Atrium\Atrium\Navigation\NavItem;
use Atrium\Atrium\Plugins\Plugin;
use Atrium\Atrium\Widgets\WidgetDefinition;
use Illuminate\Http\Request;

class UnauthorizedPlugin extends Plugin
{
    public function key(): string
    {
        return 'restricted';
    }

    public function authorize(Request $request): bool
    {
        return false;
    }

    public function navigation(): array
    {
        return [
            NavItem::make('Secret')->url('/atrium/secret'),
        ];
    }

    public function widgets(): array
    {
        return [
            WidgetDefinition::make('restricted.widget'),
        ];
    }
}
