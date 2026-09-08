---
name: atrium-development
description: >
  Configure and apply the Atrium dashboard package in Laravel applications,
  including the authorization gate, plugins, widgets, dashboards, and the
  shared Blade component library.
license: MIT
metadata:
  author: Jay Fletcher
---

# Atrium

Use this skill when a Laravel application needs to integrate the Atrium package, or when a package needs to expose itself inside an Atrium dashboard.

## Primary Goal

- apply the `jayi/atrium` public API in the smallest correct way

## Workflow

### 1. Inspect the Laravel app context

- confirm the app is a Laravel project with `jayi/atrium` installed
- read `config/atrium.php` if it has been published
- check whether a `viewAtrium` gate is already defined

### 2. Install and open the dashboard

```bash
php artisan atrium:install
```

Atrium denies access outside the local environment until its gate is defined. Add to a service provider:

```php
Gate::define('viewAtrium', fn ($user) => $user->is_admin);
```

The dashboard serves from `config('atrium.path')`, which defaults to `/atrium`.

### 3. Add a plugin

A plugin is how anything appears in the dashboard. Generate one with `php artisan atrium:plugin BillingPlugin`, then extend `Atrium\Atrium\Plugins\Plugin` and implement only the methods needed:

- `navigation()` returns `NavItem` objects for the sidebar
- `routes()` registers routes inside Atrium's group, so the prefix, middleware, and route name prefix already apply
- `widgets()` returns `WidgetDefinition` objects offered in the widget picker
- `settings()` returns a `SettingsPanel` for the settings page
- `search()` returns a `SearchSource` for the command palette
- `authorize(Request $request)` hides the whole plugin when it returns false

Register it one of two ways. Packages declare the class in their `composer.json` under `extra.atrium.plugins` and Atrium discovers it. Applications call `Atrium::plugin(BillingPlugin::class)` in a service provider's `boot()` method.

### 4. Use the component library

Components are namespaced Blade components that work anywhere, in the dashboard shell or in the application's own pages, with no Livewire dependency:

`card`, `stat`, `table` (with `table.row`, `table.cell`), `button`, `badge`, `alert`, `modal`, `dropdown`, `tabs`, `tab-panel`, `empty-state`, `page-header`, `section`, `form.input`, `form.select`, `form.textarea`, `form.checkbox`.

```blade
<x-atrium::card title="Revenue">
    <x-atrium::stat label="Total" value="$48,120" change="+12%" trend="up" />
</x-atrium::card>
```

Wrap a page in the shell with `<x-atrium::layout>`, which exposes `brand`, `topbar`, `topbarEnd`, `breadcrumbs`, `header`, `footer`, and `sidebarFooter` slots.

### 5. Theme without rebuilding assets

Atrium ships one compiled stylesheet whose values are all CSS custom properties. Anything under `config('atrium.theme')` is emitted as `--atrium-{key}`. No Tailwind build is required in the host application.

## Rules, References, and Templates

Read before executing:

- `config/atrium.php` for `path`, `domain`, `middleware`, `gate`, `discover`, `plugins`, `disabled`, and `theme`
- the package README for the full plugin and component reference

## Key Behaviors

- **Widgets are offered, not placed.** Returning a `WidgetDefinition` makes a widget available in the picker. Only a user adding it puts it on a dashboard. Never tell a user a widget will appear automatically.
- **Users keep multiple dashboards.** Each belongs to one user; a dashboard can be marked shared so everyone sees it. Only the owner can modify one.
- **Uninstalled plugins degrade gracefully.** A placement whose definition is gone is skipped rather than breaking the page.
- **Host applications stay in control.** `plugins` adds, `disabled` hides by key, and `discover: false` turns discovery off.

## Examples

- Add a "Billing" section to an existing dashboard: create a plugin with `navigation()` and `routes()`, register it in a provider, done.
- Offer a revenue widget: return a `WidgetDefinition` from `widgets()` with a view and a `resolve()` callback, then tell the user to add it from the widget picker.
- Use Atrium's components on a non-dashboard page: use `<x-atrium::card>` directly; no shell or plugin is needed.

## Anti-patterns

- do not document package internals here; keep the skill focused on adoption in Laravel apps
- do not place widgets on a user's dashboard programmatically as a convenience
- do not register plugin routes outside the plugin's `routes()` method, which would skip Atrium's middleware and prefix
- do not require a Tailwind build in the host app; the shipped stylesheet is self-contained
