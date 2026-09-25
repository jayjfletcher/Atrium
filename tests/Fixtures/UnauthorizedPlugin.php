<?php

declare(strict_types=1);

namespace JayI\Atrium\Tests\Fixtures;

use Illuminate\Http\Request;
use JayI\Atrium\Navigation\NavItem;
use JayI\Atrium\Plugins\Plugin;
use JayI\Atrium\Widgets\WidgetDefinition;

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
