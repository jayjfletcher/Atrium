<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use JayI\Atrium\Actions\CreateDashboardAction;
use JayI\Atrium\Actions\DeleteDashboardAction;
use JayI\Atrium\Actions\SaveDashboardLayoutAction;
use JayI\Atrium\Actions\UpdateDashboardAction;
use JayI\Atrium\Contracts\ActionFinishedEvent;
use JayI\Atrium\Contracts\ActionStartingEvent;
use JayI\Atrium\Contracts\ModelLifecycleEvent;
use JayI\Atrium\Events\Action\DashboardCreatedActionEvent;
use JayI\Atrium\Events\Action\DashboardCreatingActionEvent;
use JayI\Atrium\Events\Action\DashboardDeletedActionEvent;
use JayI\Atrium\Events\Action\DashboardLayoutSavedActionEvent;
use JayI\Atrium\Events\Action\DashboardLayoutSavingActionEvent;
use JayI\Atrium\Events\Action\DashboardUpdatedActionEvent;
use JayI\Atrium\Events\Action\DashboardUpdatingActionEvent;
use JayI\Atrium\Events\Model\DashboardCreatingEvent;
use JayI\Atrium\Events\Model\DashboardUpdatedEvent;
use JayI\Atrium\Events\Model\DashboardWidgetCreatedEvent;
use JayI\Atrium\Models\Dashboard;
use JayI\Atrium\Models\DashboardWidget;
use JayI\Atrium\Plugins\PluginRegistry;
use JayI\Atrium\Tests\Fixtures\AlphaPlugin;
use JayI\Atrium\Tests\Fixtures\Models\TeamDashboard;
use Workbench\App\Models\User;

/**
 * Record every event of a kind, in order.
 *
 * @param  class-string  $kind
 * @return ArrayObject<int, object>
 */
function recordEvents(string $kind): ArrayObject
{
    /** @var ArrayObject<int, object> $seen */
    $seen = new ArrayObject;

    Event::listen($kind, function (object $event) use ($seen): void {
        $seen->append($event);
    });

    return $seen;
}

function actor(string $email = 'actor@example.com'): User
{
    return User::forceCreate(['name' => 'Actor', 'email' => $email, 'password' => bcrypt('x')]);
}

it('fires every lifecycle event of a dashboard', function (): void {
    $seen = recordEvents(ModelLifecycleEvent::class);

    $dashboard = Dashboard::query()->create(['name' => 'Ops']);
    $dashboard->update(['name' => 'Renamed']);
    Dashboard::query()->find($dashboard->id);
    $dashboard->replicate();
    $dashboard->delete();

    $hooks = collect($seen)
        ->filter(fn (ModelLifecycleEvent $event): bool => $event->model() instanceof Dashboard)
        ->map(fn (ModelLifecycleEvent $event): string => $event->hook())
        ->unique()
        ->values()
        ->all();

    expect($hooks)->toEqualCanonicalizing([
        'retrieved', 'creating', 'created', 'updating', 'updated', 'saving', 'saved', 'deleting', 'deleted', 'replicating',
    ]);
});

it('fires every lifecycle event of a widget placement', function (): void {
    $dashboard = Dashboard::query()->create(['name' => 'Ops']);

    $seen = recordEvents(ModelLifecycleEvent::class);

    $widget = $dashboard->widgets()->create(['widget_key' => 'alpha.stats']);
    $widget->update(['grid_width' => 6]);
    DashboardWidget::query()->find($widget->id);
    $widget->replicate();
    $widget->delete();

    $hooks = collect($seen)
        ->filter(fn (ModelLifecycleEvent $event): bool => $event->model() instanceof DashboardWidget)
        ->map(fn (ModelLifecycleEvent $event): string => $event->hook())
        ->unique()
        ->values()
        ->all();

    expect($hooks)->toEqualCanonicalizing([
        'retrieved', 'creating', 'created', 'updating', 'updated', 'saving', 'saved', 'deleting', 'deleted', 'replicating',
    ]);
});

it('carries the model as a typed property', function (): void {
    $dashboard = Dashboard::query()->create(['name' => 'Ops']);

    Event::fake([DashboardUpdatedEvent::class, DashboardWidgetCreatedEvent::class]);

    $dashboard->update(['name' => 'Renamed']);
    $widget = $dashboard->widgets()->create(['widget_key' => 'alpha.stats']);

    Event::assertDispatched(
        DashboardUpdatedEvent::class,
        fn (DashboardUpdatedEvent $event): bool => $event->dashboard->name === 'Renamed' && $event->hook() === 'updated',
    );
    Event::assertDispatched(
        DashboardWidgetCreatedEvent::class,
        fn (DashboardWidgetCreatedEvent $event): bool => $event->widget->is($widget) && $event->model() === $event->widget,
    );
});

it('fires the dashboard events for a dashboard subclass', function (): void {
    Event::fake([DashboardCreatingEvent::class]);

    TeamDashboard::query()->create(['name' => 'Team']);

    Event::assertDispatched(DashboardCreatingEvent::class, fn (DashboardCreatingEvent $event): bool => $event->dashboard instanceof TeamDashboard);
});

it('lets a creating listener stop a dashboard being created', function (): void {
    Event::listen(DashboardCreatingEvent::class, fn (): bool => false);

    $dashboard = Dashboard::query()->create(['name' => 'Ops']);

    expect($dashboard->exists)->toBeFalse()
        ->and(Dashboard::query()->count())->toBe(0);
});

it('starts and finishes every action once, in order', function (): void {
    app(PluginRegistry::class)->register(AlphaPlugin::class);

    $starts = recordEvents(ActionStartingEvent::class);
    $stops = recordEvents(ActionFinishedEvent::class);

    $dashboard = app(CreateDashboardAction::class)->execute(['name' => 'Ops'], actor());
    app(UpdateDashboardAction::class)->execute($dashboard, ['name' => 'Renamed']);
    app(SaveDashboardLayoutAction::class)->execute($dashboard, [['widget_key' => 'alpha.stats']]);
    app(DeleteDashboardAction::class)->execute($dashboard);

    expect(collect($starts)->map(fn (object $event): string => class_basename($event))->all())->toBe([
        'DashboardCreatingActionEvent', 'DashboardUpdatingActionEvent', 'DashboardLayoutSavingActionEvent', 'DashboardDeletingActionEvent',
    ])->and(collect($stops)->map(fn (object $event): string => class_basename($event))->all())->toBe([
        'DashboardCreatedActionEvent', 'DashboardUpdatedActionEvent', 'DashboardLayoutSavedActionEvent', 'DashboardDeletedActionEvent',
    ]);
});

it('gives every action exactly one start and one finish event', function (): void {
    $actions = glob(dirname(__DIR__, 2).'/src/Actions/*Action.php') ?: [];

    $unpaired = [];

    foreach ($actions as $path) {
        if (basename($path) === 'Action.php') {
            continue;
        }

        $source = (string) file_get_contents($path);
        preg_match_all('/([A-Za-z]+ActionEvent)::dispatch/', $source, $matches);

        $kinds = array_map(
            fn (string $event): string => is_subclass_of('JayI\\Atrium\\Events\\Action\\'.$event, ActionStartingEvent::class) ? 'start' : 'finish',
            $matches[1],
        );

        sort($kinds);

        if ($kinds !== ['finish', 'start']) {
            $unpaired[] = basename($path, '.php');
        }
    }

    expect($actions)->not->toBeEmpty()
        ->and($unpaired)->toBe([]);
});

it('carries the input on the start event and the result on the finish event', function (): void {
    app(PluginRegistry::class)->register(AlphaPlugin::class);

    Event::fake([
        DashboardCreatingActionEvent::class, DashboardCreatedActionEvent::class,
        DashboardUpdatingActionEvent::class, DashboardUpdatedActionEvent::class,
        DashboardLayoutSavingActionEvent::class, DashboardLayoutSavedActionEvent::class,
        DashboardDeletedActionEvent::class,
    ]);

    $user = actor();

    $dashboard = app(CreateDashboardAction::class)->execute(['name' => 'Operations'], $user);
    app(UpdateDashboardAction::class)->execute($dashboard, ['name' => 'Renamed']);
    app(SaveDashboardLayoutAction::class)->execute($dashboard, [['widget_key' => 'alpha.stats', 'grid_width' => 6]]);
    app(DeleteDashboardAction::class)->execute($dashboard);

    expect($dashboard->isOwnedBy($user))->toBeTrue();

    Event::assertDispatched(
        DashboardCreatingActionEvent::class,
        fn (DashboardCreatingActionEvent $event): bool => $event->data === ['name' => 'Operations'] && $event->owner?->is($user) === true,
    );
    Event::assertDispatched(DashboardCreatedActionEvent::class, fn (DashboardCreatedActionEvent $event): bool => $event->dashboard->is($dashboard));
    Event::assertDispatched(DashboardUpdatingActionEvent::class, fn (DashboardUpdatingActionEvent $event): bool => $event->data === ['name' => 'Renamed']);
    Event::assertDispatched(DashboardUpdatedActionEvent::class, fn (DashboardUpdatedActionEvent $event): bool => $event->dashboard->name === 'Renamed');
    Event::assertDispatched(
        DashboardLayoutSavingActionEvent::class,
        fn (DashboardLayoutSavingActionEvent $event): bool => $event->widgets === [['widget_key' => 'alpha.stats', 'grid_width' => 6]],
    );
    Event::assertDispatched(DashboardLayoutSavedActionEvent::class, fn (DashboardLayoutSavedActionEvent $event): bool => $event->widgetKeys === ['alpha.stats']);
    Event::assertDispatched(DashboardDeletedActionEvent::class, fn (DashboardDeletedActionEvent $event): bool => $event->dashboard->is($dashboard));
});

it('creates a shared dashboard with no owner', function (): void {
    $dashboard = app(CreateDashboardAction::class)->execute(
        ['name' => 'Company', 'is_shared' => true],
        actor('shared@example.com'),
    );

    expect($dashboard->is_shared)->toBeTrue()
        ->and($dashboard->owner_id)->toBeNull();
});

it('starts before the work and finishes only once it is committed', function (): void {
    $countAtStart = null;

    Event::listen(DashboardCreatingActionEvent::class, function () use (&$countAtStart): void {
        $countAtStart = Dashboard::query()->count();
    });

    $finished = recordEvents(DashboardCreatedActionEvent::class);

    DB::transaction(function () use ($finished): void {
        app(CreateDashboardAction::class)->execute(['name' => 'Ops'], actor());

        expect($finished)->toHaveCount(0);
    });

    expect($countAtStart)->toBe(0)
        ->and($finished)->toHaveCount(1);
});

it('starts a create that rolled back but never finishes it', function (): void {
    $starts = recordEvents(DashboardCreatingActionEvent::class);
    $stops = recordEvents(DashboardCreatedActionEvent::class);

    try {
        DB::transaction(function (): void {
            app(CreateDashboardAction::class)->execute(['name' => 'Doomed'], actor('rollback@example.com'));

            throw new RuntimeException('caller aborts after the action');
        });
    } catch (RuntimeException) {
        // expected
    }

    expect(Dashboard::query()->count())->toBe(0)
        ->and($starts)->toHaveCount(1)
        ->and($stops)->toHaveCount(0);
});

it('exposes execute as the entry point and keeps handle protected', function (): void {
    $handle = new ReflectionMethod(CreateDashboardAction::class, 'handle');

    expect($handle->isProtected())->toBeTrue();
});
