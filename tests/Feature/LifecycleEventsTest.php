<?php

declare(strict_types=1);

use Atrium\Atrium\Events\Dashboard\DashboardCreatedEvent;
use Atrium\Atrium\Events\Dashboard\DashboardCreatingEvent;
use Atrium\Atrium\Events\Dashboard\DashboardDeletedEvent;
use Atrium\Atrium\Events\Dashboard\DashboardRetrievedEvent;
use Atrium\Atrium\Events\Dashboard\DashboardSavedEvent;
use Atrium\Atrium\Events\Dashboard\DashboardUpdatedEvent;
use Atrium\Atrium\Events\DashboardWidget\DashboardWidgetCreatedEvent;
use Atrium\Atrium\Events\DashboardWidget\DashboardWidgetDeletedEvent;
use Atrium\Atrium\Models\Dashboard;
use Atrium\Atrium\Models\DashboardWidget;
use Illuminate\Support\Facades\Event;

it('dispatches lifecycle events when a dashboard is created', function (): void {
    Event::fake([DashboardCreatingEvent::class, DashboardCreatedEvent::class, DashboardSavedEvent::class]);

    Dashboard::query()->create(['name' => 'Ops']);

    Event::assertDispatched(DashboardCreatingEvent::class);
    Event::assertDispatched(DashboardCreatedEvent::class);
    Event::assertDispatched(DashboardSavedEvent::class);
});

it('dispatches an update event carrying the model', function (): void {
    $dashboard = Dashboard::query()->create(['name' => 'Ops']);

    Event::fake([DashboardUpdatedEvent::class]);

    $dashboard->update(['name' => 'Renamed']);

    Event::assertDispatched(
        DashboardUpdatedEvent::class,
        fn (DashboardUpdatedEvent $event): bool => $event->dashboard->name === 'Renamed',
    );
});

it('dispatches a delete event', function (): void {
    $dashboard = Dashboard::query()->create(['name' => 'Ops']);

    Event::fake([DashboardDeletedEvent::class]);

    $dashboard->delete();

    Event::assertDispatched(DashboardDeletedEvent::class);
});

it('dispatches a retrieved event when a dashboard is read back', function (): void {
    Dashboard::query()->create(['name' => 'Ops']);

    Event::fake([DashboardRetrievedEvent::class]);

    Dashboard::query()->first();

    Event::assertDispatched(DashboardRetrievedEvent::class);
});

it('dispatches lifecycle events for widget placements too', function (): void {
    $dashboard = Dashboard::query()->create(['name' => 'Ops']);

    Event::fake([DashboardWidgetCreatedEvent::class, DashboardWidgetDeletedEvent::class]);

    $widget = $dashboard->widgets()->create(['widget_key' => 'alpha.stats']);
    $widget->delete();

    Event::assertDispatched(DashboardWidgetCreatedEvent::class);
    Event::assertDispatched(DashboardWidgetDeletedEvent::class);
});

it('maps every lifecycle hook on both models', function (string $model, int $expected): void {
    $dispatches = (new ReflectionClass($model))
        ->newInstanceWithoutConstructor();

    $property = new ReflectionProperty($model, 'dispatchesEvents');

    $map = $property->getValue($dispatches);

    expect($map)->toHaveCount($expected);

    foreach ($map as $event) {
        expect(class_exists($event))->toBeTrue("Missing event class [{$event}].");
    }
})->with([
    [Dashboard::class, 10],
    [DashboardWidget::class, 10],
]);
