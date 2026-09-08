# Atrium

[![Tests](https://github.com/jayi/atrium/actions/workflows/tests.yml/badge.svg)](https://github.com/jayi/atrium/actions/workflows/tests.yml)
[![Latest Version](https://img.shields.io/packagist/v/jayi/atrium.svg)](https://packagist.org/packages/jayi/atrium)
[![License](https://img.shields.io/packagist/l/jayi/atrium.svg)](LICENSE.md)

A plug-and-play dashboard for Laravel that other packages can extend.

Atrium gives you a dashboard shell and a shared component library. Packages register a plugin and their navigation, pages, settings, widgets, and search results appear automatically. Unlike Nova, Atrium never forces your screens through a resource abstraction or a single layout. The components are the product; the shell is scaffolding you can take or leave.

## Installation

```bash
composer require jayi/atrium
php artisan atrium:install
```

Atrium denies access outside the local environment until you define its gate. Add this to a service provider:

```php
use Illuminate\Support\Facades\Gate;

Gate::define('viewAtrium', fn ($user) => $user->is_admin);
```

Then visit `/atrium`.

## Writing a plugin

Generate a plugin class:

```bash
php artisan atrium:plugin BillingPlugin
```

Every method is optional. A plugin that only adds one sidebar link implements one method.

```php
use Atrium\Atrium\Navigation\NavItem;
use Atrium\Atrium\Plugins\Plugin;
use Atrium\Atrium\Widgets\WidgetDefinition;
use Illuminate\Support\Facades\Route;

class BillingPlugin extends Plugin
{
    public function navigation(): array
    {
        return [
            NavItem::make('Invoices')
                ->route('atrium.billing.invoices')
                ->group('Billing')
                ->badge(fn () => Invoice::unpaid()->count()),
        ];
    }

    public function routes(): void
    {
        // Registered inside Atrium's group, so the prefix, middleware,
        // and route name prefix already apply.
        Route::get('billing/invoices', InvoiceController::class)->name('billing.invoices');
    }

    public function widgets(): array
    {
        return [
            WidgetDefinition::make('billing.revenue')
                ->label('Revenue')
                ->description('Revenue for the current month.')
                ->defaultSize(4, 2)
                ->view('billing::widgets.revenue')
                ->resolve(fn (array $settings) => ['total' => Invoice::revenue()]),
        ];
    }
}
```

### Registering the plugin

Packages declare their plugin in `composer.json` and Atrium discovers it on install:

```json
{
    "extra": {
        "atrium": {
            "plugins": ["Acme\\Billing\\BillingPlugin"]
        }
    }
}
```

Applications register their own plugins in a service provider's `boot()` method:

```php
Atrium::plugin(BillingPlugin::class);
```

Host applications stay in control. Add plugin classes to `plugins` in `config/atrium.php`, list keys under `disabled` to hide a discovered plugin, or set `discover` to `false` to turn discovery off entirely.

## Widgets are offered, never placed

Returning a `WidgetDefinition` makes a widget *available* in the widget picker. It does not put it on anyone's dashboard. Only a user adding it does that.

Users keep as many dashboards as they like. Each dashboard belongs to one user, and a dashboard can be marked shared so everyone sees it. When a plugin is uninstalled, its placements are skipped rather than breaking the page.

## The component library

Components work anywhere in your application, inside the dashboard or outside it. They carry no Livewire dependency.

```blade
<x-atrium::card title="Revenue" subtitle="Last 30 days">
    <x-atrium::stat label="Total" value="$48,120" change="+12%" trend="up" />
</x-atrium::card>

<x-atrium::table striped>
    <x-slot:head>
        <x-atrium::table.row>
            <x-atrium::table.cell heading>Name</x-atrium::table.cell>
        </x-atrium::table.row>
    </x-slot:head>

    @foreach ($users as $user)
        <x-atrium::table.row>
            <x-atrium::table.cell>{{ $user->name }}</x-atrium::table.cell>
        </x-atrium::table.row>
    @endforeach
</x-atrium::table>
```

Available components:

**Layout and content**: `card`, `section`, `page-header`, `empty-state`, `stat`, `table` (with `table.row` and `table.cell`), `pagination`, `breadcrumbs`

**Controls**: `button`, `badge`, `kbd`, `avatar`, `toggle`, `tooltip`, `dropdown`, `modal`, `tabs`, `tab-panel`

**Feedback**: `alert`, `progress`, `spinner`, `skeleton`

**Forms**: `form.input`, `form.textarea`, `form.select`, `form.checkbox`, `form.radio`, `form.file`

The markup is adapted from [Penguin UI](https://www.penguinui.com) under the MIT License. See [CREDITS.md](CREDITS.md).

Publish them to take ownership:

```bash
php artisan vendor:publish --tag=atrium-views
```

## Using the shell for your own pages

```blade
<x-atrium::layout title="Invoices">
    <x-slot:header>
        <x-atrium::page-header title="Invoices" />
    </x-slot:header>

    Your page content.
</x-atrium::layout>
```

Every region is a slot: `brand`, `topbar`, `topbarEnd`, `breadcrumbs`, `header`, `footer`, and `sidebarFooter`.

## Theming

Atrium ships one compiled stylesheet built with Tailwind CSS 4. **No Tailwind build is required in your application** — the package compiles its own, and a host application's Tailwind setup is untouched.

Every color and radius is a design token expressed as a CSS custom property, so retheming means overriding variables rather than rebuilding assets:

```php
// config/atrium.php
'theme' => [
    'primary' => '#0f766e',
    'on-primary' => '#ffffff',
],
```

Anything you add here is emitted as `--color-{key}` on the dashboard. The full token list is in `resources/css/atrium.css`. Dark mode follows a `dark` class on a parent element.

To rebuild the stylesheet while working on the package itself:

```bash
npm install
npm run build:css
```

## Events

Atrium announces everything it does, so a host application can react without patching the package.

**Action events** carry business context and fire only after the write commits:

```php
use Atrium\Atrium\Events\Actions\DashboardLayoutSavedActionEvent;

Event::listen(DashboardLayoutSavedActionEvent::class, function ($event) {
    // $event->dashboard, $event->widgetKeys
});
```

Available: `DashboardCreatedActionEvent`, `DashboardUpdatedActionEvent`, `DashboardDeletedActionEvent`, and `DashboardLayoutSavedActionEvent`.

**Lifecycle events** fire on every database operation and carry just the model. Each Eloquent hook maps to its own class under `Atrium\Atrium\Events\Dashboard` and `Atrium\Atrium\Events\DashboardWidget`, covering `retrieved`, `creating`, `created`, `updating`, `updated`, `saving`, `saved`, `deleting`, `deleted`, and `replicating`.

Reach for action events for business side effects such as notifications and integrations. Use lifecycle events for data concerns such as auditing and derived columns.

## Extending Atrium's own behavior

Atrium's writes follow the same pattern its plugins should. Requests own validation and authorization and expose their work through `persist()`; controllers only pass through. The work itself lives in an action, which wraps the write in a transaction and dispatches its event after commit.

```php
$dashboard = app(CreateDashboardAction::class)->execute(['name' => 'Operations'], $user);
```

Actions expose `execute()` and keep `handle()` protected, so there is one entry point per action.

## Configuration

| Key | Purpose |
| --- | --- |
| `path` | The URI the dashboard is served from. Defaults to `atrium`. |
| `domain` | Serve the dashboard from a dedicated subdomain. |
| `middleware` | The middleware stack applied to all Atrium and plugin routes. |
| `gate` | The gate ability checked before the dashboard is shown. |
| `discover` | Whether to discover plugins from installed packages. |
| `plugins` | Plugin classes registered manually. |
| `disabled` | Plugin keys to hide. |
| `theme` | Values emitted as CSS custom properties. |

## Commands

| Command | Purpose |
| --- | --- |
| `atrium:install` | Publish the config and assets, and print the gate stub. |
| `atrium:plugins` | List the plugins currently registered. |
| `atrium:plugin` | Generate a new plugin class. |

## Testing

```bash
composer test
```

Browser tests run separately, because they need Playwright:

```bash
npm install
composer test:browser
```

They drive a real Chromium against the dashboard and cover the interactions that HTTP tests cannot reach: entering edit mode, removing and resizing a widget, dragging to reorder, the widget picker, and the search palette.

To see a working dashboard locally:

```bash
composer build && composer serve
```

The workbench ships a demo plugin exercising every plugin surface.

## Credits

- [Jay Fletcher](https://github.com/jayi)

## License

The MIT License (MIT). See [License File](LICENSE.md) for more information.
