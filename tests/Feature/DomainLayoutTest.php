<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Relations\Relation;
use JayI\Atrium\Domains\Access\AccessServiceProvider;
use JayI\Atrium\Domains\Dashboard\DashboardServiceProvider;
use JayI\Atrium\Domains\Dashboard\Models\DashboardModel;
use JayI\Atrium\Domains\Dashboard\Models\DashboardWidgetModel;
use JayI\Atrium\Domains\DomainServiceProvider;
use JayI\Atrium\Domains\Navigation\NavigationServiceProvider;
use JayI\Atrium\Domains\Plugins\PluginsServiceProvider;
use JayI\Atrium\Domains\Search\SearchServiceProvider;
use JayI\Atrium\Domains\Settings\SettingsServiceProvider;
use JayI\Atrium\Domains\Widgets\WidgetsServiceProvider;

it('registers every domain service provider', function (string $provider): void {
    expect(app()->getProvider($provider))->toBeInstanceOf($provider);
})->with([
    DomainServiceProvider::class,
    AccessServiceProvider::class,
    DashboardServiceProvider::class,
    NavigationServiceProvider::class,
    PluginsServiceProvider::class,
    SearchServiceProvider::class,
    SettingsServiceProvider::class,
    WidgetsServiceProvider::class,
]);

it('keeps writing the class names the models were stored under before they moved', function (): void {
    expect(new DashboardModel()->getMorphClass())->toBe('JayI\Atrium\Models\Dashboard')
        ->and(new DashboardWidgetModel()->getMorphClass())->toBe('JayI\Atrium\Models\DashboardWidget');
});

it('resolves a value stored under a model\'s old class name', function (): void {
    expect(Relation::getMorphedModel('JayI\Atrium\Models\Dashboard'))->toBe(DashboardModel::class)
        ->and(Relation::getMorphedModel('JayI\Atrium\Models\DashboardWidget'))->toBe(DashboardWidgetModel::class);

    $parent = DashboardModel::query()->create(['name' => 'Parent']);

    // A row written before the move, owned through the old class name.
    $child = DashboardModel::query()->create([
        'name' => 'Child',
        'owner_type' => 'JayI\Atrium\Models\Dashboard',
        'owner_id' => $parent->getKey(),
    ]);

    $owner = DashboardModel::query()->findOrFail($child->getKey())->owner;

    expect($owner)->toBeInstanceOf(DashboardModel::class)
        ->and($owner?->getKey())->toBe($parent->getKey())
        ->and($child->isOwnedBy($parent))->toBeTrue();
});
