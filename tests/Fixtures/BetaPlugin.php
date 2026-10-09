<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Tests\Fixtures;

use RefactorCircus\Atrium\Domains\Navigation\Data\NavItem;
use RefactorCircus\Atrium\Domains\Plugins\Support\Plugin;
use RefactorCircus\Atrium\Domains\Widgets\Data\WidgetDefinition;

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
