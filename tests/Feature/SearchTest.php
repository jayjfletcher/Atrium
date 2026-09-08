<?php

declare(strict_types=1);

use Atrium\Atrium\Plugins\PluginRegistry;
use Atrium\Atrium\Search\SearchRegistry;
use Atrium\Atrium\Search\SearchResult;
use Atrium\Atrium\Search\SearchSource;
use Atrium\Atrium\Tests\Fixtures\FullPlugin;
use Illuminate\Http\Request;

beforeEach(function (): void {
    app()->detectEnvironment(fn (): string => 'local');
});

it('aggregates results across plugin sources', function (): void {
    app(PluginRegistry::class)->register(FullPlugin::class);

    app(SearchRegistry::class)->add(
        SearchSource::make('app')->using(fn (string $q): array => [SearchResult::make('App '.$q, '/app')]),
    );

    $results = app(SearchRegistry::class)->search(Request::create('/atrium/search'), 'invoices');

    expect($results)->toHaveCount(2);
});

it('returns nothing for an empty query without calling sources', function (): void {
    $called = false;

    app(SearchRegistry::class)->add(
        SearchSource::make('app')->using(function (string $q) use (&$called): array {
            $called = true;

            return [];
        }),
    );

    $results = app(SearchRegistry::class)->search(Request::create('/atrium/search'), '   ');

    expect($results)->toBe([])->and($called)->toBeFalse();
});

it('skips sources whose authorize callback denies', function (): void {
    app(SearchRegistry::class)->add(
        SearchSource::make('blocked')
            ->authorize(fn (): bool => false)
            ->using(fn (string $q): array => [SearchResult::make('Nope', '/nope')]),
    );

    expect(app(SearchRegistry::class)->search(Request::create('/atrium/search'), 'x'))->toBe([]);
});

it('serves results as json from the search endpoint', function (): void {
    app(PluginRegistry::class)->register(FullPlugin::class);

    $this->getJson('/atrium/search?q=orders')
        ->assertOk()
        ->assertJsonPath('data.0.title', 'Match for orders')
        ->assertJsonPath('data.0.subtitle', 'From the full plugin');
});

it('returns an empty payload for a blank query', function (): void {
    $this->getJson('/atrium/search?q=')->assertOk()->assertJsonPath('data', []);
});
