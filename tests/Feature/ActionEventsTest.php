<?php

declare(strict_types=1);

use Atrium\Atrium\Actions\CreateDashboardAction;
use Atrium\Atrium\Actions\DeleteDashboardAction;
use Atrium\Atrium\Actions\SaveDashboardLayoutAction;
use Atrium\Atrium\Actions\UpdateDashboardAction;
use Atrium\Atrium\Events\Actions\DashboardCreatedActionEvent;
use Atrium\Atrium\Events\Actions\DashboardDeletedActionEvent;
use Atrium\Atrium\Events\Actions\DashboardLayoutSavedActionEvent;
use Atrium\Atrium\Events\Actions\DashboardUpdatedActionEvent;
use Atrium\Atrium\Models\Dashboard;
use Atrium\Atrium\Plugins\PluginRegistry;
use Atrium\Atrium\Tests\Fixtures\AlphaPlugin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Workbench\App\Models\User;

function actor(string $email = 'actor@example.com'): User
{
    return User::forceCreate(['name' => 'Actor', 'email' => $email, 'password' => bcrypt('x')]);
}

it('creates a dashboard and announces it', function (): void {
    Event::fake([DashboardCreatedActionEvent::class]);

    $user = actor();

    $dashboard = app(CreateDashboardAction::class)->execute(['name' => 'Operations'], $user);

    expect($dashboard->name)->toBe('Operations')
        ->and($dashboard->isOwnedBy($user))->toBeTrue();

    Event::assertDispatched(
        DashboardCreatedActionEvent::class,
        fn (DashboardCreatedActionEvent $event): bool => $event->dashboard->is($dashboard),
    );
});

it('creates a shared dashboard with no owner', function (): void {
    $dashboard = app(CreateDashboardAction::class)->execute(
        ['name' => 'Company', 'is_shared' => true],
        actor('shared@example.com'),
    );

    expect($dashboard->is_shared)->toBeTrue()
        ->and($dashboard->owner_id)->toBeNull();
});

it('updates a dashboard and announces it', function (): void {
    Event::fake([DashboardUpdatedActionEvent::class]);

    $dashboard = Dashboard::query()->create(['name' => 'Before']);

    $updated = app(UpdateDashboardAction::class)->execute($dashboard, ['name' => 'After']);

    expect($updated->name)->toBe('After');

    Event::assertDispatched(DashboardUpdatedActionEvent::class);
});

it('deletes a dashboard and announces it', function (): void {
    Event::fake([DashboardDeletedActionEvent::class]);

    $dashboard = Dashboard::query()->create(['name' => 'Temp']);

    app(DeleteDashboardAction::class)->execute($dashboard);

    expect(Dashboard::query()->count())->toBe(0);

    Event::assertDispatched(DashboardDeletedActionEvent::class);
});

it('saves a layout and announces the widget keys in order', function (): void {
    Event::fake([DashboardLayoutSavedActionEvent::class]);

    app(PluginRegistry::class)->register(AlphaPlugin::class);

    $dashboard = Dashboard::query()->create(['name' => 'Ops']);

    app(SaveDashboardLayoutAction::class)->execute($dashboard, [
        ['widget_key' => 'alpha.stats', 'grid_width' => 6],
    ]);

    expect($dashboard->widgets()->pluck('widget_key')->all())->toBe(['alpha.stats']);

    Event::assertDispatched(
        DashboardLayoutSavedActionEvent::class,
        fn (DashboardLayoutSavedActionEvent $event): bool => $event->widgetKeys === ['alpha.stats'],
    );
});

it('does not announce a create that rolled back', function (): void {
    Event::fake([DashboardCreatedActionEvent::class]);

    try {
        DB::transaction(function (): void {
            app(CreateDashboardAction::class)->execute(['name' => 'Doomed'], actor('rollback@example.com'));

            throw new RuntimeException('caller aborts after the action');
        });
    } catch (RuntimeException) {
        // expected
    }

    expect(Dashboard::query()->count())->toBe(0);

    Event::assertNotDispatched(DashboardCreatedActionEvent::class);
});

it('exposes execute as the entry point and keeps handle protected', function (): void {
    $handle = new ReflectionMethod(CreateDashboardAction::class, 'handle');

    expect($handle->isProtected())->toBeTrue();
});
