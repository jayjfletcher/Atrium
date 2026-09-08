<?php

declare(strict_types=1);

use Atrium\Atrium\Navigation\NavigationRegistry;
use Atrium\Atrium\Navigation\NavItem;
use Atrium\Atrium\Plugins\PluginRegistry;
use Atrium\Atrium\Tests\Fixtures\AlphaPlugin;
use Atrium\Atrium\Tests\Fixtures\BetaPlugin;
use Atrium\Atrium\Tests\Fixtures\UnauthorizedPlugin;
use Illuminate\Http\Request;

function nav(): NavigationRegistry
{
    return app(NavigationRegistry::class);
}

it('collects navigation items from every registered plugin', function (): void {
    app(PluginRegistry::class)->registerMany([AlphaPlugin::class, BetaPlugin::class]);

    $labels = array_map(
        fn (NavItem $item): string => $item->label,
        nav()->items(Request::create('/atrium')),
    );

    expect($labels)->toContain('Alpha Home')->toContain('Beta Home');
});

it('sorts navigation items by their sort value', function (): void {
    app(PluginRegistry::class)->registerMany([AlphaPlugin::class, BetaPlugin::class]);

    $labels = array_map(
        fn (NavItem $item): string => $item->label,
        nav()->items(Request::create('/atrium')),
    );

    // Beta sorts at 5, Alpha at 10.
    expect($labels)->toBe(['Beta Home', 'Alpha Home']);
});

it('groups navigation items', function (): void {
    app(PluginRegistry::class)->registerMany([AlphaPlugin::class, BetaPlugin::class]);

    $groups = nav()->grouped(Request::create('/atrium'));

    expect($groups)->toHaveKey('Main')
        ->and($groups['Main'])->toHaveCount(2);
});

it('hides navigation from unauthorized plugins', function (): void {
    app(PluginRegistry::class)->registerMany([AlphaPlugin::class, UnauthorizedPlugin::class]);

    $labels = array_map(
        fn (NavItem $item): string => $item->label,
        nav()->items(Request::create('/atrium')),
    );

    expect($labels)->toContain('Alpha Home')->not->toContain('Secret');
});

it('hides an individual item whose own authorize callback denies', function (): void {
    nav()->add(NavItem::make('Hidden')->url('/hidden')->authorize(fn (): bool => false));
    nav()->add(NavItem::make('Shown')->url('/shown'));

    $labels = array_map(
        fn (NavItem $item): string => $item->label,
        nav()->items(Request::create('/atrium')),
    );

    expect($labels)->toBe(['Shown']);
});

it('resolves a badge callback lazily', function (): void {
    $calls = 0;

    $item = NavItem::make('Inbox')->url('/inbox')->badge(function () use (&$calls) {
        $calls++;

        return 7;
    });

    expect($calls)->toBe(0)
        ->and($item->resolveBadge())->toBe(7)
        ->and($calls)->toBe(1);
});

it('marks an item active when the current url matches', function (): void {
    $item = NavItem::make('Reports')->url('http://localhost/atrium/reports');

    expect($item->isActive(Request::create('http://localhost/atrium/reports')))->toBeTrue()
        ->and($item->isActive(Request::create('http://localhost/atrium/other')))->toBeFalse();
});
