# Model Lifecycle Events

Every Atrium model maps its Eloquent hooks to typed event classes via `$dispatchesEvents`, separate from the package's domain action events (see [[actions-events]]).

## Convention

- Map each hook to a `{Model}{Hook}Event` in `Atrium\Atrium\Events\{Model}\`; the event carries just the model and extends the package's `Event` base, so it dispatches after commit.

```php
protected $dispatchesEvents = [
    'retrieved' => DashboardRetrievedEvent::class,
    'creating'  => DashboardCreatingEvent::class,
    'created'   => DashboardCreatedEvent::class,
    'updating'  => DashboardUpdatingEvent::class,
    // updated, saving, saved, deleting, deleted, replicating
];
```

- Cover the full lifecycle the model actually has. Atrium's models do not use soft deletes, so they omit `restoring`, `restored`, `trashed`, `forceDeleting`, and `forceDeleted`; a model that adds soft deletes must add those five.
- A test asserts the map is complete and that every class it names exists, so a renamed event fails the suite rather than silently going unheard.

## Lifecycle vs domain ActionEvents

| | Model lifecycle events | Domain `*ActionEvent` |
|---|---|---|
| Fires | automatically on every database operation | explicitly from an Action |
| Carries | the model only | model plus business context |
| Listen for | data concerns (audit, computed fields, cascades) | business side effects (notifications, integrations) |

Default to **ActionEvents** for business logic; reach for lifecycle events only for data-level concerns.

## Why this matters for a package

Atrium's models live in someone else's application. Lifecycle events give host applications and plugins a supported way to observe dashboard data without patching the model or overriding the package's classes.
