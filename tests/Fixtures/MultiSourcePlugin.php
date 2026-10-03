<?php

declare(strict_types=1);

namespace JayI\Atrium\Tests\Fixtures;

use JayI\Atrium\Domains\Plugins\Support\Plugin;
use JayI\Atrium\Domains\Search\Data\SearchResult;
use JayI\Atrium\Domains\Search\Data\SearchSource;

/**
 * A plugin that searches two kinds of thing, as two sources.
 */
class MultiSourcePlugin extends Plugin
{
    public function key(): string
    {
        return 'multi';
    }

    /**
     * @return array<int, SearchSource>
     */
    public function search(): array
    {
        return [
            SearchSource::make('multi-people')->label('People')->using(fn (string $query): array => array_map(
                fn (int $i): SearchResult => SearchResult::make("Person {$i}", "/people/{$i}"),
                range(1, 5),
            )),
            SearchSource::make('multi-places')->label('Places')->using(fn (string $query): array => [
                SearchResult::make('Place 1', '/places/1'),
            ]),
        ];
    }
}
