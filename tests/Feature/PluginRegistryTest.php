<?php

declare(strict_types=1);

use Atrium\Atrium\Exceptions\InvalidPluginException;
use Atrium\Atrium\Plugins\Plugin;
use Atrium\Atrium\Plugins\PluginRegistry;
use Atrium\Atrium\Tests\Fixtures\AlphaPlugin;
use Atrium\Atrium\Tests\Fixtures\BetaPlugin;
use Atrium\Atrium\Tests\Fixtures\NotAPlugin;
use Atrium\Atrium\Tests\Fixtures\UnauthorizedPlugin;
use Illuminate\Http\Request;

function registry(): PluginRegistry
{
    return app(PluginRegistry::class);
}

it('registers a plugin from a class string', function (): void {
    registry()->register(AlphaPlugin::class);

    expect(registry()->has('alpha'))->toBeTrue()
        ->and(registry()->get('alpha'))->toBeInstanceOf(AlphaPlugin::class);
});

it('registers a plugin from an instance', function (): void {
    registry()->register(new BetaPlugin);

    expect(registry()->get('beta'))->toBeInstanceOf(BetaPlugin::class);
});

it('keys plugins so each is registered once', function (): void {
    registry()->register(AlphaPlugin::class)->register(AlphaPlugin::class);

    expect(registry()->all())->toHaveCount(1);
});

it('rejects a class that does not implement the plugin contract', function (): void {
    registry()->register(NotAPlugin::class);
})->throws(InvalidPluginException::class, 'must implement');

it('rejects a plugin class that does not exist', function (): void {
    registry()->register('App\\Nope\\MissingPlugin');
})->throws(InvalidPluginException::class, 'does not exist');

it('rejects two different plugins claiming the same key', function (): void {
    $duplicate = new class extends Plugin
    {
        public function key(): string
        {
            return 'alpha';
        }
    };

    registry()->register(AlphaPlugin::class)->register($duplicate);
})->throws(InvalidPluginException::class, 'already registered');

it('skips plugins the host app disabled', function (): void {
    registry()->disable(['alpha'])->registerMany([AlphaPlugin::class, BetaPlugin::class]);

    expect(registry()->has('alpha'))->toBeFalse()
        ->and(registry()->has('beta'))->toBeTrue();
});

it('filters unauthorized plugins out of the authorized list', function (): void {
    registry()->registerMany([AlphaPlugin::class, UnauthorizedPlugin::class]);

    $authorized = registry()->authorized(Request::create('/atrium'));

    expect($authorized)->toHaveKey('alpha')
        ->and($authorized)->not->toHaveKey('restricted')
        ->and(registry()->all())->toHaveCount(2);
});
