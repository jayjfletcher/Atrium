<?php

declare(strict_types=1);

use Atrium\Atrium\AtriumServiceProvider;
use Atrium\Atrium\Plugins\PluginRegistry;
use Atrium\Atrium\Support\Discovery\ComposerPluginDiscovery;
use Atrium\Atrium\Tests\Fixtures\AlphaPlugin;
use Atrium\Atrium\Tests\Fixtures\BetaPlugin;
use Illuminate\Filesystem\Filesystem;

function fakeVendor(array $packages): string
{
    $path = sys_get_temp_dir().'/atrium-discovery-'.uniqid();

    mkdir($path.'/composer', 0777, true);

    file_put_contents(
        $path.'/composer/installed.json',
        json_encode(['packages' => $packages], JSON_THROW_ON_ERROR),
    );

    return $path;
}

function discovery(array $packages): ComposerPluginDiscovery
{
    return new ComposerPluginDiscovery(new Filesystem, fakeVendor($packages));
}

it('discovers plugins declared by installed packages', function (): void {
    $plugins = discovery([
        ['name' => 'acme/billing', 'extra' => ['atrium' => ['plugins' => [AlphaPlugin::class]]]],
        ['name' => 'acme/reports', 'extra' => ['atrium' => ['plugins' => [BetaPlugin::class]]]],
    ])->discover();

    expect($plugins)->toBe([AlphaPlugin::class, BetaPlugin::class]);
});

it('accepts a single plugin declared as a string', function (): void {
    $plugins = discovery([
        ['name' => 'acme/billing', 'extra' => ['atrium' => ['plugins' => AlphaPlugin::class]]],
    ])->discover();

    expect($plugins)->toBe([AlphaPlugin::class]);
});

it('ignores packages that declare no atrium plugins', function (): void {
    $plugins = discovery([
        ['name' => 'acme/unrelated'],
        ['name' => 'acme/other', 'extra' => ['laravel' => ['providers' => ['Something']]]],
        ['name' => 'acme/empty', 'extra' => ['atrium' => []]],
    ])->discover();

    expect($plugins)->toBe([]);
});

it('returns nothing when the composer manifest is missing', function (): void {
    $discovery = new ComposerPluginDiscovery(new Filesystem, '/does/not/exist');

    expect($discovery->discover())->toBe([]);
});

it('deduplicates a plugin declared by more than one package', function (): void {
    $plugins = discovery([
        ['name' => 'acme/one', 'extra' => ['atrium' => ['plugins' => [AlphaPlugin::class]]]],
        ['name' => 'acme/two', 'extra' => ['atrium' => ['plugins' => [AlphaPlugin::class]]]],
    ])->discover();

    expect($plugins)->toBe([AlphaPlugin::class]);
});

it('registers plugins listed in config', function (): void {
    config()->set('atrium.plugins', [AlphaPlugin::class]);

    app()->forgetInstance(PluginRegistry::class);

    $provider = app()->getProvider(AtriumServiceProvider::class);
    $provider->boot();

    expect(app(PluginRegistry::class)->has('alpha'))->toBeTrue();
});
