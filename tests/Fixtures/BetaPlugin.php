<?php

declare(strict_types=1);

namespace JayI\Atrium\Tests\Fixtures;

use JayI\Atrium\Navigation\NavItem;
use JayI\Atrium\Plugins\Plugin;
use JayI\Atrium\Widgets\WidgetDefinition;

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
