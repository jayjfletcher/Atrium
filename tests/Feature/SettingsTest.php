<?php

declare(strict_types=1);

use Atrium\Atrium\Plugins\PluginRegistry;
use Atrium\Atrium\Settings\SettingsPanel;
use Atrium\Atrium\Settings\SettingsRegistry;
use Atrium\Atrium\Tests\Fixtures\FullPlugin;
use Atrium\Atrium\Tests\Fixtures\UnauthorizedPlugin;
use Illuminate\Http\Request;

beforeEach(function (): void {
    app()->detectEnvironment(fn (): string => 'local');
});

it('collects settings panels from plugins', function (): void {
    app(PluginRegistry::class)->register(FullPlugin::class);

    $panels = app(SettingsRegistry::class)->panels(Request::create('/atrium/settings'));

    expect($panels)->toHaveCount(1)
        ->and($panels[0]->label)->toBe('Full Settings');
});

it('sorts panels by their sort value', function (): void {
    $registry = app(SettingsRegistry::class);

    $registry->add(SettingsPanel::make('late')->label('Late')->sort(50));
    $registry->add(SettingsPanel::make('early')->label('Early')->sort(10));

    $labels = array_map(
        fn (SettingsPanel $panel): string => $panel->label,
        $registry->panels(Request::create('/atrium/settings')),
    );

    expect($labels)->toBe(['Early', 'Late']);
});

it('excludes panels from unauthorized plugins', function (): void {
    app(PluginRegistry::class)->registerMany([FullPlugin::class, UnauthorizedPlugin::class]);

    $panels = app(SettingsRegistry::class)->panels(Request::create('/atrium/settings'));

    expect($panels)->toHaveCount(1);
});

it('lists panels on the settings page', function (): void {
    app(PluginRegistry::class)->register(FullPlugin::class);

    $this->get('/atrium/settings')->assertOk()->assertSee('Full Settings');
});

it('renders an individual panel', function (): void {
    app(PluginRegistry::class)->register(FullPlugin::class);

    $this->get('/atrium/settings/full')
        ->assertOk()
        ->assertSee('Full plugin settings body');
});

it('returns 404 for an unknown panel', function (): void {
    $this->get('/atrium/settings/nope')->assertNotFound();
});

it('shows an empty state when no panels exist', function (): void {
    $this->get('/atrium/settings')->assertOk()->assertSee(__('atrium::atrium.no_settings_panels'));
});
