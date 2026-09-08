<?php

declare(strict_types=1);

namespace Workbench\App\Atrium;

use Atrium\Atrium\Navigation\NavItem;
use Atrium\Atrium\Plugins\Plugin;
use Atrium\Atrium\Search\SearchResult;
use Atrium\Atrium\Search\SearchSource;
use Atrium\Atrium\Settings\SettingsPanel;
use Atrium\Atrium\Widgets\WidgetDefinition;
use Illuminate\Support\Facades\Route;
use Workbench\App\Models\User;

/**
 * Demonstrates every plugin surface Atrium offers, so `composer serve`
 * shows a working dashboard rather than an empty shell.
 */
class DemoPlugin extends Plugin
{
    public function key(): string
    {
        return 'demo';
    }

    public function label(): string
    {
        return 'Demo';
    }

    public function navigation(): array
    {
        return [
            NavItem::make('Overview')
                ->route('atrium.dashboard')
                ->group('Demo')
                ->sort(10),

            NavItem::make('Users')
                ->route('atrium.demo.users')
                ->group('Demo')
                ->sort(20)
                ->badge(fn (): int => User::query()->count()),
        ];
    }

    public function routes(): void
    {
        Route::get('demo/users', function () {
            return view('workbench::demo.users', [
                'users' => User::query()->orderBy('name')->get(),
            ]);
        })->name('demo.users');
    }

    public function settings(): ?SettingsPanel
    {
        return SettingsPanel::make('demo')
            ->label('Demo settings')
            ->description('Shows how a plugin contributes to the settings page.')
            ->view('workbench::demo.settings');
    }

    /**
     * Widgets are offered here. Nothing appears on a dashboard until
     * someone picks it from the widget picker.
     */
    public function widgets(): array
    {
        return [
            WidgetDefinition::make('demo.users')
                ->label('User count')
                ->description('How many users exist in the workbench app.')
                ->defaultSize(3, 1)
                ->view('workbench::demo.widgets.users')
                ->resolve(fn (array $settings): array => [
                    'count' => User::query()->count(),
                ]),

            WidgetDefinition::make('demo.welcome')
                ->label('Welcome note')
                ->description('A static card explaining what Atrium is.')
                ->defaultSize(6, 2)
                ->view('workbench::demo.widgets.welcome'),
        ];
    }

    public function search(): ?SearchSource
    {
        return SearchSource::make('demo')
            ->label('Users')
            ->using(fn (string $query): array => User::query()
                ->where('name', 'like', '%'.$query.'%')
                ->limit(5)
                ->get()
                ->map(fn (User $user): SearchResult => SearchResult::make($user->name, route('atrium.demo.users'))
                    ->subtitle($user->email)
                    ->group('Users'))
                ->all());
    }
}
