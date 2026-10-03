<?php

declare(strict_types=1);

namespace JayI\Atrium\Tests\Fixtures;

use JayI\Atrium\Domains\Navigation\Data\NavItem;
use JayI\Atrium\Domains\Plugins\Support\Plugin;
use JayI\Atrium\Domains\Widgets\Data\WidgetDefinition;

class AlphaPlugin extends Plugin
{
    public function key(): string
    {
        return 'alpha';
    }

    public function label(): string
    {
        return 'Alpha';
    }

    public function navigation(): array
    {
        return [
            NavItem::make('Alpha Home')->url('/atrium/alpha')->group('Main')->sort(10),
        ];
    }

    public function widgets(): array
    {
        return [
            WidgetDefinition::make('alpha.stats')
                ->label('Alpha Stats')
                ->description('Headline numbers for Alpha.')
                ->view('alpha-stats'),
        ];
    }
}
