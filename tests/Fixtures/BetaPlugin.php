<?php

declare(strict_types=1);

namespace Atrium\Atrium\Tests\Fixtures;

use Atrium\Atrium\Navigation\NavItem;
use Atrium\Atrium\Plugins\Plugin;
use Atrium\Atrium\Widgets\WidgetDefinition;

class BetaPlugin extends Plugin
{
    public function key(): string
    {
        return 'beta';
    }

    public function navigation(): array
    {
        return [
            NavItem::make('Beta Home')->url('/atrium/beta')->group('Main')->sort(5),
        ];
    }

    public function widgets(): array
    {
        return [
            WidgetDefinition::make('beta.chart')->label('Beta Chart'),
        ];
    }
}
