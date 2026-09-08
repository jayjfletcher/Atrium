<?php

declare(strict_types=1);

use Atrium\Atrium\Exceptions\DuplicateWidgetException;
use Atrium\Atrium\Models\Dashboard;
use Atrium\Atrium\Plugins\Plugin;
use Atrium\Atrium\Plugins\PluginRegistry;
use Atrium\Atrium\Tests\Fixtures\AlphaPlugin;
use Atrium\Atrium\Tests\Fixtures\BetaPlugin;
use Atrium\Atrium\Tests\Fixtures\UnauthorizedPlugin;
use Atrium\Atrium\Widgets\WidgetDefinition;
use Atrium\Atrium\Widgets\WidgetRegistry;
use Illuminate\Http\Request;

function widgets(): WidgetRegistry
{
    return app(WidgetRegistry::class);
}

it('collects widget definitions from registered plugins', function (): void {
    app(PluginRegistry::class)->registerMany([AlphaPlugin::class, BetaPlugin::class]);

    expect(widgets()->all())->toHaveKeys(['alpha.stats', 'beta.chart']);
});

it('offers widgets without placing them on any dashboard', function (): void {
    app(PluginRegistry::class)->register(AlphaPlugin::class);

    $dashboard = Dashboard::query()->create(['name' => 'Ops', 'is_shared' => true]);

    expect(widgets()->all())->toHaveKey('alpha.stats')
        ->and($dashboard->widgets()->count())->toBe(0);
});

it('excludes widgets belonging to unauthorized plugins', function (): void {
    app(PluginRegistry::class)->registerMany([AlphaPlugin::class, UnauthorizedPlugin::class]);

    $available = widgets()->available(Request::create('/atrium'));

    expect($available)->toHaveKey('alpha.stats')
        ->and($available)->not->toHaveKey('restricted.widget');
});

it('excludes a widget whose own authorize callback denies', function (): void {
    widgets()->add(WidgetDefinition::make('app.private')->authorize(fn (): bool => false));
    widgets()->add(WidgetDefinition::make('app.public'));

    $available = widgets()->available(Request::create('/atrium'));

    expect($available)->toHaveKey('app.public')
        ->and($available)->not->toHaveKey('app.private');
});

it('rejects two plugins registering the same widget key', function (): void {
    $conflicting = new class extends Plugin
    {
        public function key(): string
        {
            return 'conflict';
        }

        public function widgets(): array
        {
            return [WidgetDefinition::make('alpha.stats')];
        }
    };

    app(PluginRegistry::class)->registerMany([AlphaPlugin::class, $conflicting]);

    widgets()->all();
})->throws(DuplicateWidgetException::class, 'already registered');

it('resolves widget data only when asked', function (): void {
    $calls = 0;

    $definition = WidgetDefinition::make('lazy')->resolve(function (array $settings) use (&$calls): array {
        $calls++;

        return ['settings' => $settings];
    });

    widgets()->add($definition);

    expect($calls)->toBe(0)
        ->and($definition->resolveData(['a' => 1]))->toBe(['settings' => ['a' => 1]])
        ->and($calls)->toBe(1);
});

it('exposes default sizing for the picker', function (): void {
    $definition = WidgetDefinition::make('sized')->defaultSize(6, 3);

    expect($definition->defaultWidth)->toBe(6)
        ->and($definition->defaultHeight)->toBe(3);
});
