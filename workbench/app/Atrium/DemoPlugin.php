<?php

declare(strict_types=1);

namespace Workbench\App\Atrium;

use Illuminate\Support\Facades\Route;
use JayI\Atrium\Domains\Navigation\Data\NavGroup;
use JayI\Atrium\Domains\Navigation\Data\NavItem;
use JayI\Atrium\Domains\Plugins\Support\Plugin;
use JayI\Atrium\Domains\Search\Data\SearchResult;
use JayI\Atrium\Domains\Search\Data\SearchSource;
use JayI\Atrium\Domains\Settings\Data\SettingsPanel;
use JayI\Atrium\Domains\Widgets\Data\WidgetDefinition;
use JayI\Atrium\Support\Icons;
use Workbench\App\Models\User;

/**
 * Demonstrates every plugin surface Atrium offers with a small online shop,
 * so `composer serve` shows a working dashboard rather than an empty shell.
 */
class DemoPlugin extends Plugin
{
    public function key(): string
    {
        return 'demo';
    }

    public function label(): string
    {
        return 'Acme Shop';
    }

    public function navigation(): array
    {
        return [
            NavItem::make('Overview')
                ->route('atrium.dashboard')
                ->icon(Icons::svg('home'))
                ->group('Workspace')
                ->sort(10),

            NavItem::make('Orders')
                ->route('atrium.demo.orders')
                ->icon(Icons::svg('shopping-cart'))
                ->group('Workspace')
                ->sort(20)
                ->badge(fn (): int => DemoData::orders()->where('status', 'pending')->count()),

            NavItem::make('Team')
                ->route('atrium.demo.users')
                ->icon(Icons::svg('users'))
                ->group('Workspace')
                ->sort(30)
                ->badge(fn (): int => User::query()->count()),

            NavItem::make('Reports')
                ->icon(Icons::svg('chart-bar'))
                ->group('Insights')
                ->sort(40)
                ->children([
                    NavItem::make('Sales')->route('atrium.demo.reports.sales'),
                    NavItem::make('Signups')->route('atrium.demo.reports.signups'),
                ]),

            NavItem::make('Billing')
                ->route('atrium.demo.billing')
                ->icon(Icons::svg('credit-card'))
                ->group('Administration')
                ->sort(50)
                ->feature('billing')
                ->can('manageBilling'),

            // The workbench's feature resolver turns `audit-log` off, so this
            // item is registered but never shown.
            NavItem::make('Audit log')
                ->url('#audit-log')
                ->icon(Icons::svg('clipboard-document-list'))
                ->group('Administration')
                ->sort(60)
                ->feature('audit-log'),
        ];
    }

    public function navigationGroups(): array
    {
        return [
            NavGroup::make('Workspace')->icon(Icons::svg('briefcase'))->sort(10),
            NavGroup::make('Insights')->icon(Icons::svg('chart-pie'))->sort(20)->feature('reports')->can('viewReports'),
            NavGroup::make('Administration')->icon(Icons::svg('cog-6-tooth'))->sort(30)->can('administer'),
        ];
    }

    public function routes(): void
    {
        Route::get('demo/orders', fn () => view('workbench::demo.orders', [
            'orders' => DemoData::orders(),
        ]))->name('demo.orders');

        Route::get('demo/users', fn () => view('workbench::demo.users', [
            'users' => User::query()->orderBy('name')->get(),
        ]))->name('demo.users');

        // One route per report, since a navigation item is active by route
        // name and each report has its own item.
        foreach (DemoData::reports() as $key => $report) {
            Route::get('demo/reports/'.$key, fn () => view('workbench::demo.report', ['report' => $report]))
                ->name('demo.reports.'.$key);
        }

        Route::get('demo/billing', fn () => view('workbench::demo.billing', [
            'invoices' => DemoData::invoices(),
        ]))->middleware('can:manageBilling')->name('demo.billing');
    }

    public function settings(): ?SettingsPanel
    {
        return SettingsPanel::make('demo')
            ->label('Shop settings')
            ->description('Store details and the feature flags this workbench runs with.')
            ->view('workbench::demo.settings')
            ->resolve(fn (): array => ['features' => DemoData::FEATURES]);
    }

    /**
     * Widgets are offered here. The workbench seeder places them on demo
     * dashboards; in a real application users pick them from the picker.
     */
    public function widgets(): array
    {
        $kpis = DemoData::kpis();

        return [
            WidgetDefinition::make('demo.revenue')
                ->label('Revenue')
                ->description('Revenue this month against last month.')
                ->defaultSize(3, 1)
                ->view('workbench::demo.widgets.stat')
                ->resolve(fn (): array => ['label' => 'Revenue this month', 'icon' => 'banknotes', ...$kpis['revenue']]),

            WidgetDefinition::make('demo.orders')
                ->label('Orders')
                ->description('Orders placed this month.')
                ->defaultSize(3, 1)
                ->view('workbench::demo.widgets.stat')
                ->resolve(fn (): array => ['label' => 'Orders this month', 'icon' => 'shopping-cart', ...$kpis['orders']]),

            WidgetDefinition::make('demo.conversion')
                ->label('Conversion rate')
                ->description('Share of storefront visits that end in an order.')
                ->defaultSize(3, 1)
                ->view('workbench::demo.widgets.stat')
                ->resolve(fn (): array => ['label' => 'Conversion rate', 'icon' => 'presentation-chart-line', ...$kpis['conversion']]),

            WidgetDefinition::make('demo.users')
                ->label('Team members')
                ->description('People with access, read from the users table.')
                ->defaultSize(3, 1)
                ->view('workbench::demo.widgets.stat')
                ->resolve(function (): array {
                    $pending = User::query()->whereNull('email_verified_at')->count();

                    return [
                        'label' => 'Team members',
                        'icon' => 'users',
                        'value' => User::query()->count(),
                        'change' => $pending.' awaiting invitation',
                        'trend' => null,
                    ];
                }),

            WidgetDefinition::make('demo.recent-orders')
                ->label('Recent orders')
                ->description('The latest orders and their status.')
                ->defaultSize(8, 2)
                ->view('workbench::demo.widgets.recent-orders')
                ->resolve(fn (): array => ['orders' => DemoData::orders()->take(5)]),

            WidgetDefinition::make('demo.services')
                ->label('Service status')
                ->description('Health of the services the shop runs on.')
                ->defaultSize(4, 2)
                ->view('workbench::demo.widgets.services')
                ->resolve(fn (): array => ['services' => DemoData::services()]),

            WidgetDefinition::make('demo.goals')
                ->label('Quarterly goals')
                ->description('Progress toward this quarter\'s targets.')
                ->defaultSize(6, 2)
                ->view('workbench::demo.widgets.goals')
                ->resolve(fn (): array => ['goals' => DemoData::goals()]),

            WidgetDefinition::make('demo.welcome')
                ->label('Welcome note')
                ->description('A card explaining what this demo shows.')
                ->defaultSize(6, 2)
                ->view('workbench::demo.widgets.welcome'),
        ];
    }

    /**
     * One source per kind of thing, so each is limited and grouped separately.
     *
     * @return array<int, SearchSource>
     */
    public function search(): array
    {
        return [
            SearchSource::make('demo.team')
                ->label('Team')
                ->description('People with access to the shop.')
                ->using(fn (string $query): array => User::query()
                    ->where(fn ($builder) => $builder->where('name', 'like', '%'.$query.'%')->orWhere('email', 'like', '%'.$query.'%'))
                    ->orderBy('name')
                    ->limit(5)
                    ->get()
                    ->map(fn (User $user): SearchResult => SearchResult::make($user->name, route('atrium.demo.users'))
                        ->subtitle($user->email)
                        ->group('Team'))
                    ->all()),

            SearchSource::make('demo.orders')
                ->label('Orders')
                ->description('Customer orders by number or customer name.')
                ->using(fn (string $query): array => DemoData::orders()
                    ->filter(fn (array $order): bool => str_contains(strtolower($order['number'].' '.$order['customer']), strtolower($query)))
                    ->map(fn (array $order): SearchResult => SearchResult::make($order['number'].' · '.$order['customer'], route('atrium.demo.orders').'#'.$order['number'])
                        ->subtitle('$'.number_format($order['total'], 2).' · '.DemoData::orderStatus($order['status'])['label'])
                        ->group('Orders'))
                    ->values()
                    ->all()),
        ];
    }
}
