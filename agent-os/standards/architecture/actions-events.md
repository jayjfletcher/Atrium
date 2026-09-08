# Actions & Events

Actions hold mutating business logic; Events announce what happened after the data is committed.

Adapted from the `mono` standard of the same name, minus its Pennant feature-gating tier: Atrium is a package and cannot require Pennant of its consumers.

## Base class — `execute()` is the entry point, `handle()` does the work

- Actions extend `Atrium\Atrium\Actions\Action`.
- `Action::execute(mixed ...$args)` delegates to your **`protected handle(...)`**. You implement `handle()`, callers call `execute()`.
- Never make `handle()` public — one entry point keeps a place to add cross-cutting behavior later.
- No static constructors (`::run()`/`::make()`). Resolve and invoke via `app(XAction::class)->execute(...)`.

```php
class CreateDashboardAction extends Action
{
    /** @param array{name: string, is_shared?: bool} $data */
    protected function handle(array $data, ?Model $owner = null): Dashboard
    {
        return DB::transaction(function () use ($data, $owner): Dashboard {
            $dashboard = Dashboard::query()->create([...]);

            DB::afterCommit(fn () => DashboardCreatedActionEvent::dispatch($dashboard));

            return $dashboard;
        });
    }
}
```

## handle() signatures

- **Always a concrete return type — never `mixed`.** Create/update returns the model; delete returns `void`; index returns a paginator or collection.
- Bound models arrive as `handle()` parameters; array payloads carry array-shape PHPDoc (`@param array{...} $data`).
- **Read actions have no transaction and no events** — they query and return.

## Transactions & events

- Wrap every **mutating** action body in `DB::transaction()`.
- Dispatch events via **`DB::afterCommit()` inside the transaction closure**, so registration sits next to the write but firing only happens after commit, never on rollback.
- When a caller needs post-commit column values, `return $model->refresh();` **outside** the transaction.

## Dependency injection

- **Do not constructor-inject the model** — pass it through `handle()` arguments. Constructor promotion is for injected services only.

## Event

- Extend `Atrium\Atrium\Events\Event`, which implements `ShouldDispatchAfterCommit`.
- A plain data carrier using constructor property promotion; it may carry more than one value.
- Naming: `{Entity}{Verb}ActionEvent` — `DashboardCreatedActionEvent`, `DashboardLayoutSavedActionEvent`.

> Why `afterCommit`: listeners (jobs, notifications, integrations) never fire for a rolled-back write. The explicit `DB::afterCommit()` is belt-and-suspenders, since the base event already defers; keep the wrapper everywhere for a uniform, visible signal at the dispatch site.

These are distinct from model **lifecycle events**. Default to ActionEvents for business logic; see [[lifecycle-events]].
