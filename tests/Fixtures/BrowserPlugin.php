<?php

declare(strict_types=1);

namespace JayI\Atrium\Tests\Fixtures;

use JayI\Atrium\Navigation\NavItem;
use JayI\Atrium\Plugins\Plugin;
use JayI\Atrium\Search\SearchResult;
use JayI\Atrium\Search\SearchSource;
use JayI\Atrium\Widgets\WidgetDefinition;

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
