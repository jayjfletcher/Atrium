<?php

declare(strict_types=1);

use JayI\Atrium\Atrium;
use JayI\Atrium\Plugins\PluginRegistry;
use JayI\Atrium\Tests\Fixtures\AlphaPlugin;

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

it('adds package styles to the head after its own stylesheet', function (): void {
    app(Atrium::class)->stylesheet('/vendor/billing/billing.css')
        ->css('.billing-wide { width: 30rem; }', 'billing')
        ->css('.billing-wide { width: 32rem; }', 'billing')
        ->css('.billing-narrow { width: 8rem; }');

    $html = $this->get('/atrium')->assertOk()->getContent();
    $head = substr($html, 0, strpos($html, '</head>'));

    expect($head)->toContain('href="/vendor/billing/billing.css"')
        ->toContain('.billing-wide { width: 32rem; }')
        ->not->toContain('width: 30rem')
        ->toContain('.billing-narrow { width: 8rem; }')
        ->and(strpos($head, 'billing.css'))->toBeGreaterThan(strpos($head, 'vendor/atrium/atrium.css'));
});
