<?php

declare(strict_types=1);

use Atrium\Atrium\Models\Dashboard;
use Atrium\Atrium\Models\DashboardWidget;
use Atrium\Atrium\Plugins\PluginRegistry;
use Atrium\Atrium\Tests\Fixtures\AlphaPlugin;
use Workbench\App\Models\User;

it('creates the dashboard tables', function (): void {
    expect(Schema::hasTable('atrium_dashboards'))->toBeTrue()
        ->and(Schema::hasTable('atrium_dashboard_widgets'))->toBeTrue();
})->skip(fn (): bool => ! class_exists(Schema::class));

it('slugs a dashboard on creation', function (): void {
    $dashboard = Dashboard::query()->create(['name' => 'Operations Overview']);

    expect($dashboard->slug)->toBe('operations-overview');
});

it('resolves a users own dashboards alongside shared ones', function (): void {
    $user = User::forceCreate([
        'name' => 'Ada',
        'email' => 'ada@example.com',
        'password' => bcrypt('secret'),
    ]);

    $other = User::forceCreate([
        'name' => 'Grace',
        'email' => 'grace@example.com',
        'password' => bcrypt('secret'),
    ]);

    Dashboard::query()->create(['name' => 'Mine', 'owner_type' => $user->getMorphClass(), 'owner_id' => $user->getKey()]);
    Dashboard::query()->create(['name' => 'Theirs', 'owner_type' => $other->getMorphClass(), 'owner_id' => $other->getKey()]);
    Dashboard::query()->create(['name' => 'Shared', 'is_shared' => true]);

    $visible = Dashboard::query()->visibleTo($user)->pluck('name')->all();

    expect($visible)->toContain('Mine')->toContain('Shared')->not->toContain('Theirs');
});

it('cascades widget deletes when a dashboard is removed', function (): void {
    $dashboard = Dashboard::query()->create(['name' => 'Temp']);

    $dashboard->widgets()->create(['widget_key' => 'alpha.stats']);

    expect(DashboardWidget::query()->count())->toBe(1);

    $dashboard->delete();

    expect(DashboardWidget::query()->count())->toBe(0);
});

it('resolves a placed widget back to its definition', function (): void {
    app(PluginRegistry::class)->register(AlphaPlugin::class);

    $dashboard = Dashboard::query()->create(['name' => 'Ops']);
    $placement = $dashboard->widgets()->create(['widget_key' => 'alpha.stats']);

    expect($placement->definition())->not->toBeNull()
        ->and($placement->definition()->label)->toBe('Alpha Stats')
        ->and($placement->isOrphaned())->toBeFalse();
});

it('treats a placement as orphaned when its plugin is gone', function (): void {
    $dashboard = Dashboard::query()->create(['name' => 'Ops']);
    $placement = $dashboard->widgets()->create(['widget_key' => 'removed.plugin.widget']);

    expect($placement->definition())->toBeNull()
        ->and($placement->isOrphaned())->toBeTrue();
});

it('knows which owner a dashboard belongs to', function (): void {
    $user = User::forceCreate([
        'name' => 'Ada',
        'email' => 'ada2@example.com',
        'password' => bcrypt('secret'),
    ]);

    $owned = Dashboard::query()->create(['name' => 'Mine', 'owner_type' => $user->getMorphClass(), 'owner_id' => $user->getKey()]);
    $shared = Dashboard::query()->create(['name' => 'Shared', 'is_shared' => true]);

    expect($owned->isOwnedBy($user))->toBeTrue()
        ->and($shared->isOwnedBy($user))->toBeFalse()
        ->and($owned->isOwnedBy(null))->toBeFalse();
});
