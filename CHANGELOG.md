# Release Notes

## [Unreleased](https://github.com/jayi/atrium/compare/v0.1.0...1.x)

### Added

- Themes: a second built-in theme, **Harbor**, beside the default **Atrium**; themes from `atrium.themes.available` and `Atrium::theme()`; and a theme switcher beside the light/dark toggle, shown behind the `atrium.themes.switcher_feature` feature (jayi/pennantplus's `ThemeSwitcherFeature` by default).
- Shared components so the packages of the suite need no markup or styles of their own: `description-list`, `chip`, `search-input`, `flash`, `banner`, `guest`, `form.combobox`, `form.actions`, a `bare` mode for form controls, and `prefix`/`suffix` slots on `form.input`.
- History components that read the audit log through jayi/foundation's `AuditTrail`: `audit-trail`, `audit.entries` and `audit.changes`. `audit-trail` renders nothing until an audit log (jayi/keen) is installed.
- `Plugin::featuresFromConfig()`, `JayI\Atrium\Support\ScreenAccess` and the `AuthorizesScreens` controller trait, so a package's plugin and screens stop copying the same feature loading and policy checks.
- A safelist of layout utilities in the compiled stylesheet, and `JayI\Atrium\Testing\AtriumStyles` for checking a package's views use only Atrium's styles.

### Fixed

- Form controls no longer render an `id` twice when one is passed.
- `audit-trail` shows a package's history only to those its history endpoint would answer.

### Changed

- Atrium now stands on [jayi/foundation](https://github.com/jayjfletcher/Foundation), the shared runtime of the suite. The `Action` base, the `ActionStartingEvent`, `ActionFinishedEvent` and `ModelLifecycleEvent` contracts and the `DispatchesModelEvents` trait moved there: import them from `JayI\Foundation\...` instead of `JayI\Atrium\...`. Atrium registers itself as the `atrium` package.

### Fixed

- Opening a dashboard by its slug (`/atrium/d/{slug}`, which the dashboard switcher links to) no longer returns 404. The route parameter was bound to the dashboard model by id.
- The dashboard switcher opens toward the page instead of off its right edge, and labels shared dashboards "Shared".
- Tooltips are no longer clipped by scrolling or `overflow: hidden` containers, such as tables: the bubble is drawn on `<body>`, fixed beside its trigger, and kept inside the viewport.

### Breaking

- The source is reorganised into domain modules under `src/Domains/{Domain}` (`Access`, `Dashboard`, `Navigation`, `Plugins`, `Search`, `Settings`, `Widgets`), mirroring the `mono` application. Almost every class moved; there are no aliases for the old names, so update `use` statements, `extends`/`implements` clauses, and any class names in a published `config/atrium.php` (`middleware`, `policies`). The models are renamed `DashboardModel` and `DashboardWidgetModel`, value objects moved into `Data\`, registries into `Services\`, and model and action events now sit together in `Domains\Dashboard\Events`. `AtriumServiceProvider`, the `Atrium` class and facade, `Actions\Action`, `Http\Requests\Request`, the event contracts (`Contracts\Action*Event`, `Contracts\ModelLifecycleEvent`), `Support\Icons` and `Console\Commands\InstallCommand` keep their names. Config keys, route names, view and component names, translation keys, publish tags, table names and the `atrium.feature` middleware alias are unchanged. The models keep their old class names (`JayI\Atrium\Models\Dashboard`, `JayI\Atrium\Models\DashboardWidget`) as morph aliases, so stored polymorphic values still resolve and new rows store the same strings. The full map:

  | Old | New |
  | --- | --- |
  | `JayI\Atrium\Access\Concerns\Gated` | `JayI\Atrium\Domains\Access\Concerns\Gated` |
  | `JayI\Atrium\Access\Gatekeeper` | `JayI\Atrium\Domains\Access\Services\Gatekeeper` |
  | `JayI\Atrium\Actions\CreateDashboardAction` | `JayI\Atrium\Domains\Dashboard\Actions\CreateDashboardAction` |
  | `JayI\Atrium\Actions\DeleteDashboardAction` | `JayI\Atrium\Domains\Dashboard\Actions\DeleteDashboardAction` |
  | `JayI\Atrium\Actions\SaveDashboardLayoutAction` | `JayI\Atrium\Domains\Dashboard\Actions\SaveDashboardLayoutAction` |
  | `JayI\Atrium\Actions\UpdateDashboardAction` | `JayI\Atrium\Domains\Dashboard\Actions\UpdateDashboardAction` |
  | `JayI\Atrium\Assets\StyleRegistry` | `JayI\Atrium\Support\StyleRegistry` |
  | `JayI\Atrium\Console\Commands\MakePluginCommand` | `JayI\Atrium\Domains\Plugins\Console\Commands\MakePluginCommand` |
  | `JayI\Atrium\Console\Commands\PluginListCommand` | `JayI\Atrium\Domains\Plugins\Console\Commands\PluginListCommand` |
  | `JayI\Atrium\Contracts\Plugin` | `JayI\Atrium\Domains\Plugins\Contracts\Plugin` |
  | `JayI\Atrium\Dashboards\DashboardManager` | `JayI\Atrium\Domains\Dashboard\Services\DashboardManager` |
  | `JayI\Atrium\Events\Action\DashboardCreatedActionEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardCreatedActionEvent` |
  | `JayI\Atrium\Events\Action\DashboardCreatingActionEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardCreatingActionEvent` |
  | `JayI\Atrium\Events\Action\DashboardDeletedActionEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardDeletedActionEvent` |
  | `JayI\Atrium\Events\Action\DashboardDeletingActionEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardDeletingActionEvent` |
  | `JayI\Atrium\Events\Action\DashboardLayoutSavedActionEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardLayoutSavedActionEvent` |
  | `JayI\Atrium\Events\Action\DashboardLayoutSavingActionEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardLayoutSavingActionEvent` |
  | `JayI\Atrium\Events\Action\DashboardUpdatedActionEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardUpdatedActionEvent` |
  | `JayI\Atrium\Events\Action\DashboardUpdatingActionEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardUpdatingActionEvent` |
  | `JayI\Atrium\Events\Model\DashboardCreatedEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardCreatedEvent` |
  | `JayI\Atrium\Events\Model\DashboardCreatingEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardCreatingEvent` |
  | `JayI\Atrium\Events\Model\DashboardDeletedEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardDeletedEvent` |
  | `JayI\Atrium\Events\Model\DashboardDeletingEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardDeletingEvent` |
  | `JayI\Atrium\Events\Model\DashboardReplicatingEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardReplicatingEvent` |
  | `JayI\Atrium\Events\Model\DashboardRetrievedEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardRetrievedEvent` |
  | `JayI\Atrium\Events\Model\DashboardSavedEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardSavedEvent` |
  | `JayI\Atrium\Events\Model\DashboardSavingEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardSavingEvent` |
  | `JayI\Atrium\Events\Model\DashboardUpdatedEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardUpdatedEvent` |
  | `JayI\Atrium\Events\Model\DashboardUpdatingEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardUpdatingEvent` |
  | `JayI\Atrium\Events\Model\DashboardWidgetCreatedEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardWidgetCreatedEvent` |
  | `JayI\Atrium\Events\Model\DashboardWidgetCreatingEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardWidgetCreatingEvent` |
  | `JayI\Atrium\Events\Model\DashboardWidgetDeletedEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardWidgetDeletedEvent` |
  | `JayI\Atrium\Events\Model\DashboardWidgetDeletingEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardWidgetDeletingEvent` |
  | `JayI\Atrium\Events\Model\DashboardWidgetReplicatingEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardWidgetReplicatingEvent` |
  | `JayI\Atrium\Events\Model\DashboardWidgetRetrievedEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardWidgetRetrievedEvent` |
  | `JayI\Atrium\Events\Model\DashboardWidgetSavedEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardWidgetSavedEvent` |
  | `JayI\Atrium\Events\Model\DashboardWidgetSavingEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardWidgetSavingEvent` |
  | `JayI\Atrium\Events\Model\DashboardWidgetUpdatedEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardWidgetUpdatedEvent` |
  | `JayI\Atrium\Events\Model\DashboardWidgetUpdatingEvent` | `JayI\Atrium\Domains\Dashboard\Events\DashboardWidgetUpdatingEvent` |
  | `JayI\Atrium\Exceptions\DuplicateWidgetException` | `JayI\Atrium\Domains\Widgets\Exceptions\DuplicateWidgetException` |
  | `JayI\Atrium\Exceptions\InvalidPluginException` | `JayI\Atrium\Domains\Plugins\Exceptions\InvalidPluginException` |
  | `JayI\Atrium\Http\Controllers\DashboardController` | `JayI\Atrium\Domains\Dashboard\Http\Controllers\DashboardController` |
  | `JayI\Atrium\Http\Controllers\DashboardCrudController` | `JayI\Atrium\Domains\Dashboard\Http\Controllers\DashboardCrudController` |
  | `JayI\Atrium\Http\Controllers\DashboardLayoutController` | `JayI\Atrium\Domains\Dashboard\Http\Controllers\DashboardLayoutController` |
  | `JayI\Atrium\Http\Controllers\SearchController` | `JayI\Atrium\Domains\Search\Http\Controllers\SearchController` |
  | `JayI\Atrium\Http\Controllers\SettingsController` | `JayI\Atrium\Domains\Settings\Http\Controllers\SettingsController` |
  | `JayI\Atrium\Http\Middleware\Authorize` | `JayI\Atrium\Domains\Access\Http\Middleware\Authorize` |
  | `JayI\Atrium\Http\Middleware\EnsureFeaturesAreEnabled` | `JayI\Atrium\Domains\Access\Http\Middleware\EnsureFeaturesAreEnabled` |
  | `JayI\Atrium\Http\Requests\DeleteDashboardRequest` | `JayI\Atrium\Domains\Dashboard\Http\Requests\DeleteDashboardRequest` |
  | `JayI\Atrium\Http\Requests\SaveDashboardLayoutRequest` | `JayI\Atrium\Domains\Dashboard\Http\Requests\SaveDashboardLayoutRequest` |
  | `JayI\Atrium\Http\Requests\StoreDashboardRequest` | `JayI\Atrium\Domains\Dashboard\Http\Requests\StoreDashboardRequest` |
  | `JayI\Atrium\Http\Requests\UpdateDashboardRequest` | `JayI\Atrium\Domains\Dashboard\Http\Requests\UpdateDashboardRequest` |
  | `JayI\Atrium\Models\Concerns\DispatchesModelEvents` | `JayI\Atrium\Support\Models\Concerns\DispatchesModelEvents` |
  | `JayI\Atrium\Models\Dashboard` | `JayI\Atrium\Domains\Dashboard\Models\DashboardModel` |
  | `JayI\Atrium\Models\DashboardWidget` | `JayI\Atrium\Domains\Dashboard\Models\DashboardWidgetModel` |
  | `JayI\Atrium\Navigation\NavGroup` | `JayI\Atrium\Domains\Navigation\Data\NavGroup` |
  | `JayI\Atrium\Navigation\NavItem` | `JayI\Atrium\Domains\Navigation\Data\NavItem` |
  | `JayI\Atrium\Navigation\NavigationRegistry` | `JayI\Atrium\Domains\Navigation\Services\NavigationRegistry` |
  | `JayI\Atrium\Plugins\Plugin` | `JayI\Atrium\Domains\Plugins\Support\Plugin` |
  | `JayI\Atrium\Plugins\PluginRegistry` | `JayI\Atrium\Domains\Plugins\Services\PluginRegistry` |
  | `JayI\Atrium\Policies\DashboardPolicy` | `JayI\Atrium\Domains\Dashboard\Policies\DashboardPolicy` |
  | `JayI\Atrium\Policies\DashboardWidgetPolicy` | `JayI\Atrium\Domains\Dashboard\Policies\DashboardWidgetPolicy` |
  | `JayI\Atrium\Policies\Policy` | `JayI\Atrium\Domains\Dashboard\Policies\Policy` |
  | `JayI\Atrium\Search\SearchRegistry` | `JayI\Atrium\Domains\Search\Services\SearchRegistry` |
  | `JayI\Atrium\Search\SearchResult` | `JayI\Atrium\Domains\Search\Data\SearchResult` |
  | `JayI\Atrium\Search\SearchSource` | `JayI\Atrium\Domains\Search\Data\SearchSource` |
  | `JayI\Atrium\Settings\SettingsPanel` | `JayI\Atrium\Domains\Settings\Data\SettingsPanel` |
  | `JayI\Atrium\Settings\SettingsRegistry` | `JayI\Atrium\Domains\Settings\Services\SettingsRegistry` |
  | `JayI\Atrium\Support\Discovery\ComposerPluginDiscovery` | `JayI\Atrium\Domains\Plugins\Services\ComposerPluginDiscovery` |
  | `JayI\Atrium\Widgets\WidgetDefinition` | `JayI\Atrium\Domains\Widgets\Data\WidgetDefinition` |
  | `JayI\Atrium\Widgets\WidgetRegistry` | `JayI\Atrium\Domains\Widgets\Services\WidgetRegistry` |
- The bundled Pennant plugin (the Feature flags page, `FeatureFlagManager`, its requests, actions and `Feature*ActionEvent` events, and the `atrium.pennant` config) moved to `jayi/pennantplus` under `JayI\PennantPlus\Atrium`. Install that package to keep the page; its settings now live under `pennantplus.atrium`.
- The PHP namespace changed from `Atrium\Atrium` to `JayI\Atrium`, matching the other `jayi/*` packages. Update `use` statements, the service provider and facade references, and any class names in your config (such as `atrium.php` middleware). The package name `jayi/atrium`, the `atrium::` view namespace, `atrium.*` config keys, route names and `atrium-*` publish tags are unchanged.
- Event classes moved and were renamed. Model events now live in `JayI\Atrium\Events\Model` (`Events\Dashboard\DashboardCreatedEvent` is now `Events\Model\DashboardCreatedEvent`, and `Events\DashboardWidget\*` moved the same way). Action events moved from `Atrium\Atrium\Events\Actions` to `JayI\Atrium\Events\Action`. Update the `use` statements of your listeners.
- The `Atrium\Atrium\Events\Event` base class was removed. Events are now final classes implementing `Contracts\ModelLifecycleEvent`, `Contracts\ActionStartingEvent` or `Contracts\ActionFinishedEvent`.
- Model events now fire synchronously as Eloquent fires the hook, rather than after commit, so `creating`, `updating`, `saving` and `deleting` listeners can cancel a write. Action finish events still wait for the commit.
- Dashboard requests are authorized through model policies. Guests can no longer create a dashboard through `POST dashboards`.
- The `Plugin` contract gained `features()` and `navigationGroups()`. Plugins extending `JayI\Atrium\Plugins\Plugin` inherit empty defaults; classes implementing the contract directly must add both.
- Child navigation items are now filtered by their own rules. A parent without a link of its own is hidden once all of its children are.

### Added

- `Atrium::css()` and `Atrium::stylesheet()` let packages add styles to the dashboard's `<head>`, after Atrium's own stylesheet.
- `icon-button`, `icon` and `status-dot` components and the Heroicons outline set (`JayI\Atrium\Support\Icons`, extensible with `Icons::register()`), with screen conventions in the README: icon-only actions, status dots with `info` kept for pending, and navigation icons.
- Navigation gating: `NavItem::can()` and `feature()` beside `authorize()`, `NavGroup` rules for whole sidebar groups (from a plugin's `navigationGroups()` or `Atrium::navigationGroup()`), and plugin `features()` that hide a plugin and 404 its routes while a feature is off.
- `Atrium::resolvePermissionsUsing()` and `Atrium::resolveFeaturesUsing()` to plug in any permission or feature-flag system. Permissions default to the Gate; features are on until a resolver is registered.
- `atrium.feature:{features}` route middleware and `Atrium::featureEnabled()`.

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
