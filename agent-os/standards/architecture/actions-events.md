# Actions & Events

Actions hold mutating business logic; Events announce what happened after the data is committed.

Adapted from the `mono` standard of the same name, minus its Pennant feature-gating tier: Atrium is a package and cannot require Pennant of its consumers.

## Base class — `execute()` is the entry point, `handle()` does the work

- Actions extend `JayI\Atrium\Actions\Action`.
- `Action::execute(mixed ...$args)` delegates to your **`protected handle(...)`**. You implement `handle()`, callers call `execute()`.
- Never make `handle()` public — one entry point keeps a place to add cross-cutting behavior later.
- No static constructors (`::run()`/`::make()`). Resolve and invoke via `app(XAction::class)->execute(...)`.

```php
class CreateDashboardAction extends Action
{
    /** @param array{name: string, is_shared?: bool} $data */
    protected function handle(array $data, ?Model $owner = null): Dashboard
    {
        DashboardCreatingActionEvent::dispatch($data, $owner);

        $dashboard = $this->perform($data, $owner);

        DashboardCreatedActionEvent::dispatch($dashboard);

        return $dashboard;
    }

    private function perform(array $data, ?Model $owner): Dashboard
    {
        return DB::transaction(fn (): Dashboard => Dashboard::query()->create([...]));
    }
}
```

## handle() signatures

- **Always a concrete return type — never `mixed`.** Create/update returns the model; delete returns `void`; index returns a paginator or collection.
- Bound models arrive as `handle()` parameters; array payloads carry array-shape PHPDoc (`@param array{...} $data`).
- **Read actions have no transaction and no events** — they query and return.

## Transactions & events

- `handle()` dispatches the **starting** event with the input, calls a private `perform()`, dispatches the **finished** event with the result, and returns it.
- Wrap every **mutating** `perform()` body in `DB::transaction()`.
- Finished events implement `ActionFinishedEvent`, which extends `ShouldDispatchAfterCommit`, so they wait for the outermost transaction to commit and never fire on rollback. An action that throws fires its starting event and no finished event.
- When a caller needs post-commit column values, `return $model->refresh();` **outside** the transaction.

## Dependency injection

- **Do not constructor-inject the model** — pass it through `handle()` arguments. Constructor promotion is for injected services only.

## Event

- `final` classes in `Events\Action` using `Dispatchable` and `SerializesModels`; no base class.
- A plain data carrier using constructor property promotion; it may carry more than one value.
- Every action has exactly one pair: `{Subject}{Verb-ing}ActionEvent` implementing `Contracts\ActionStartingEvent` (carries the input) and `{Subject}{Verb-ed}ActionEvent` implementing `Contracts\ActionFinishedEvent` (carries the result) — `DashboardLayoutSavingActionEvent` / `DashboardLayoutSavedActionEvent`. A test enforces the pairing.

These are distinct from model **lifecycle events**. Default to ActionEvents for business logic; see [[lifecycle-events]].
