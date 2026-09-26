# Release Notes

## [Unreleased](https://github.com/jayi/atrium/compare/v0.1.0...1.x)

### Breaking

- The PHP namespace changed from `Atrium\Atrium` to `JayI\Atrium`, matching the other `jayi/*` packages. Update `use` statements, the service provider and facade references, and any class names in your config (such as `atrium.php` middleware). The package name `jayi/atrium`, the `atrium::` view namespace, `atrium.*` config keys, route names and `atrium-*` publish tags are unchanged.
- Event classes moved and were renamed. Model events now live in `JayI\Atrium\Events\Model` (`Events\Dashboard\DashboardCreatedEvent` is now `Events\Model\DashboardCreatedEvent`, and `Events\DashboardWidget\*` moved the same way). Action events moved from `Atrium\Atrium\Events\Actions` to `JayI\Atrium\Events\Action`. Update the `use` statements of your listeners.
- The `Atrium\Atrium\Events\Event` base class was removed. Events are now final classes implementing `Contracts\ModelLifecycleEvent`, `Contracts\ActionStartingEvent` or `Contracts\ActionFinishedEvent`.
- Model events now fire synchronously as Eloquent fires the hook, rather than after commit, so `creating`, `updating`, `saving` and `deleting` listeners can cancel a write. Action finish events still wait for the commit.
- Dashboard requests are authorized through model policies. Guests can no longer create a dashboard through `POST dashboards`.

### Added

- Every action now fires a start event before its work, carrying its input: `DashboardCreatingActionEvent`, `DashboardUpdatingActionEvent`, `DashboardDeletingActionEvent` and `DashboardLayoutSavingActionEvent`.
- `ModelLifecycleEvent`, `ActionStartingEvent` and `ActionFinishedEvent` contracts, to listen to a whole family of events at once. Model events expose `model()` and `hook()`.
- The `DispatchesModelEvents` trait, so an application's subclass of `Dashboard` fires the package's `Dashboard*` events.
- `DashboardPolicy` and `DashboardWidgetPolicy`, registered from the new `atrium.policies` config key. The owner of a dashboard may do anything, everyone may view a shared dashboard, and widget placements defer to their dashboard through the Gate. Every dashboard request, and the dashboard's edit controls, check them on top of the `atrium.gate` gate.

### Changed

- Visual refresh of the shell and every component: a zinc palette, an inset content panel over a new `canvas` token, and quieter buttons, inputs, cards, tables and badges.
- The sidebar collapses to an icon rail with hover labels and child flyouts, folds its groups, and expands child items in place.
- Dark mode is built in, with a light / dark / system switcher in the topbar. Search focuses with ⌘K / Ctrl K.
- Requires `laravel/framework` instead of `illuminate/support`, since the package uses form requests, events, queues and views from the framework.


## [v0.1.0](https://github.com/jayi/atrium/compare/...v0.1.0) - 202x-xx-xx

Initial pre-release.
