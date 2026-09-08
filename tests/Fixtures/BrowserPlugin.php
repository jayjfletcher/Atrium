<?php

declare(strict_types=1);

namespace Atrium\Atrium\Tests\Fixtures;

use Atrium\Atrium\Navigation\NavItem;
use Atrium\Atrium\Plugins\Plugin;
use Atrium\Atrium\Search\SearchResult;
use Atrium\Atrium\Search\SearchSource;
use Atrium\Atrium\Widgets\WidgetDefinition;

class BrowserPlugin extends Plugin
{
    public function key(): string
    {
        return 'browser';
    }

    public function navigation(): array
    {
        return [
            NavItem::make('Browser Page')->url('/atrium')->group('Testing')->sort(50),
        ];
    }

    public function widgets(): array
    {
        return [
            WidgetDefinition::make('browser.widget')
                ->label('Browser Widget')
                ->description('Used by the browser test suite.')
                ->view('browser-widget'),
        ];
    }

    public function search(): ?SearchSource
    {
        return SearchSource::make('browser')
            ->label('Browser')
            ->using(fn (string $query): array => [
                SearchResult::make('Result for '.$query, '/atrium')->subtitle('From the browser plugin'),
            ]);
    }
}
