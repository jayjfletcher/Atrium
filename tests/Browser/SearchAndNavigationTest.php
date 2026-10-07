<?php

declare(strict_types=1);

use JayI\Atrium\Domains\Plugins\Services\PluginRegistry;
use JayI\Atrium\Domains\Search\Data\SearchResult;
use JayI\Atrium\Domains\Search\Data\SearchSource;
use JayI\Atrium\Domains\Search\Services\SearchRegistry;
use JayI\Atrium\Tests\Fixtures\AlphaPlugin;
use JayI\Atrium\Tests\Fixtures\BrowserPlugin;

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
    // A closure written in a test file cannot run in another process, as
    // the test case class it is scoped to only exists in this one.
    config()->set('atrium.search.concurrency', 'sync');

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

it('collapses the sidebar to an icon rail and remembers it across pages', function (): void {
    visit('/atrium')
        ->click('@sidebar-toggle')
        ->assertScript('document.documentElement.dataset.atriumSidebar', 'collapsed')
        ->navigate('/atrium/settings')
        ->assertScript('document.documentElement.dataset.atriumSidebar', 'collapsed')
        ->click('@sidebar-toggle')
        ->assertScript('document.documentElement.dataset.atriumSidebar === undefined', true);
});

it('switches to dark mode and keeps it across pages', function (): void {
    visit('/atrium')
        ->click('@appearance-toggle')
        ->click('@theme-dark')
        ->assertScript('document.documentElement.classList.contains("dark")', true)
        ->navigate('/atrium/settings')
        ->assertScript('document.documentElement.classList.contains("dark")', true)
        ->click('@appearance-toggle')
        ->click('@theme-light')
        ->assertScript('document.documentElement.classList.contains("dark")', false);
});

it('switches theme beside the dark mode toggle and keeps it across pages', function (): void {
    visit('/atrium')
        ->assertScript('document.documentElement.dataset.atriumTheme', 'atrium')
        ->click('@theme-switcher')
        ->click('@palette-harbor')
        ->assertScript('document.documentElement.dataset.atriumTheme', 'harbor')
        ->assertScript('getComputedStyle(document.documentElement).getPropertyValue("--color-primary").trim()', '#0d9488')
        ->navigate('/atrium/settings')
        ->assertScript('document.documentElement.dataset.atriumTheme', 'harbor')
        ->click('@theme-switcher')
        ->click('@palette-atrium')
        ->assertScript('document.documentElement.dataset.atriumTheme', 'atrium');
});
