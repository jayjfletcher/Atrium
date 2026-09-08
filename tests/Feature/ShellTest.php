<?php

declare(strict_types=1);

use Atrium\Atrium\Atrium;
use Atrium\Atrium\Plugins\PluginRegistry;
use Atrium\Atrium\Tests\Fixtures\AlphaPlugin;

beforeEach(function (): void {
    app()->detectEnvironment(fn (): string => 'local');
});

it('renders the dashboard shell', function (): void {
    $this->get('/atrium')
        ->assertOk()
        ->assertSee('<aside', false)
        ->assertSee('id="atrium-main"', false);
});

it('renders plugin navigation in the sidebar', function (): void {
    app(PluginRegistry::class)->register(AlphaPlugin::class);

    $this->get('/atrium')->assertOk()->assertSee('Alpha Home');
});

it('emits theme config as css custom properties', function (): void {
    config()->set('atrium.theme', ['primary' => '#ff0000', 'on-primary' => '#ffffff']);

    $this->get('/atrium')
        ->assertOk()
        ->assertSee('--color-primary: #ff0000', false)
        ->assertSee('--color-on-primary: #ffffff', false);
});

it('links the published stylesheet', function (): void {
    $this->get('/atrium')->assertOk()->assertSee('vendor/atrium/atrium.css', false);
});

it('shows an empty state when no dashboard exists', function (): void {
    $this->get('/atrium')->assertOk()->assertSee(__('atrium::atrium.no_widgets'));
});

it('reads the dashboard path from config', function (): void {
    expect(app(Atrium::class)->path())->toBe('atrium');
});
