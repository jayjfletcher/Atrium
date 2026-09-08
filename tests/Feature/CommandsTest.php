<?php

declare(strict_types=1);

use Atrium\Atrium\Plugins\PluginRegistry;
use Atrium\Atrium\Tests\Fixtures\AlphaPlugin;

it('publishes config and assets and explains the gate', function (): void {
    $this->artisan('atrium:install')
        ->expectsOutputToContain('denies access outside the local environment')
        ->expectsOutputToContain("Gate::define('viewAtrium'")
        ->assertSuccessful();
});

it('lists registered plugins', function (): void {
    app(PluginRegistry::class)->register(AlphaPlugin::class);

    $this->artisan('atrium:plugins')
        ->expectsOutputToContain('alpha')
        ->assertSuccessful();
});

it('warns when no plugins are registered', function (): void {
    $this->artisan('atrium:plugins')
        ->expectsOutputToContain('No Atrium plugins are registered')
        ->assertSuccessful();
});

it('generates a plugin class from the stub', function (): void {
    $path = app_path('Atrium/BillingPlugin.php');

    $this->artisan('atrium:plugin', ['name' => 'BillingPlugin'])->assertSuccessful();

    expect(file_exists($path))->toBeTrue();

    $contents = (string) file_get_contents($path);

    expect($contents)->toContain('class BillingPlugin extends Plugin')
        ->toContain('public function navigation(): array')
        ->toContain('public function widgets(): array');

    @unlink($path);
});
