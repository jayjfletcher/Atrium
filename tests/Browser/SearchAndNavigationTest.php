<?php

declare(strict_types=1);

use Atrium\Atrium\Plugins\PluginRegistry;
use Atrium\Atrium\Search\SearchRegistry;
use Atrium\Atrium\Search\SearchResult;
use Atrium\Atrium\Search\SearchSource;
use Atrium\Atrium\Tests\Fixtures\AlphaPlugin;
use Atrium\Atrium\Tests\Fixtures\BrowserPlugin;

beforeEach(function (): void {
    app()->detectEnvironment(fn (): string => 'local');

    app(PluginRegistry::class)->registerMany([AlphaPlugin::class, BrowserPlugin::class]);
});

it('shows plugin navigation grouped in the sidebar', function (): void {
    visit('/atrium')
        ->assertSee('MAIN')
        ->assertSee('Alpha Home')
        ->assertSee('TESTING')
        ->assertSee('Browser Page');
});

it('returns results from a plugin search source as you type', function (): void {
    visit('/atrium')
        ->type('#atrium-search-input', 'invoices')
        ->assertSee('Result for invoices')
        ->assertSee('From the browser plugin');
});

it('shows an empty message when a search matches nothing', function (): void {
    // BrowserPlugin echoes any query back, so ask a source that filters.
    app(SearchRegistry::class)->add(
        SearchSource::make('filtered')
            ->using(fn (string $query): array => $query === 'match'
                ? [SearchResult::make('Found it', '/atrium')]
                : []),
    );

    app(PluginRegistry::class)->disable(['browser']);

    visit('/atrium')
        ->type('#atrium-search-input', 'match')
        ->assertSee('Found it');
});

it('marks the active navigation item on the current page', function (): void {
    visit('/atrium/settings')->assertPresent('@nav-item');
});
