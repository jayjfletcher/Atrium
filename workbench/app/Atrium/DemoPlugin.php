<?php

declare(strict_types=1);

namespace Workbench\App\Atrium;

use Illuminate\Support\Facades\Route;
use JayI\Atrium\Navigation\NavItem;
use JayI\Atrium\Plugins\Plugin;
use JayI\Atrium\Search\SearchResult;
use JayI\Atrium\Search\SearchSource;
use JayI\Atrium\Settings\SettingsPanel;
use JayI\Atrium\Widgets\WidgetDefinition;
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
                ->icon(self::icon('M2.25 12l8.954-8.955a1.126 1.126 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'))
                ->group('Demo')
                ->sort(10),

            NavItem::make('Users')
                ->route('atrium.demo.users')
                ->icon(self::icon('M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z'))
                ->group('Demo')
                ->sort(20)
                ->badge(fn (): int => User::query()->count()),

            NavItem::make('Reports')
                ->icon(self::icon('M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z'))
                ->group('Demo')
                ->sort(30)
                ->children([
                    NavItem::make('Sales')->url('#sales'),
                    NavItem::make('Signups')->url('#signups'),
                ]),
        ];
    }

    /**
     * Wraps a Heroicons outline path, which is the markup NavItem::icon() takes.
     */
    private static function icon(string $path): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="'.$path.'" /></svg>';
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
