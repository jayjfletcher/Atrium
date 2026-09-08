<?php

declare(strict_types=1);

namespace Atrium\Atrium\Actions;

use Atrium\Atrium\Events\Actions\DashboardLayoutSavedActionEvent;
use Atrium\Atrium\Models\Dashboard;
use Illuminate\Support\Facades\DB;

class SaveDashboardLayoutAction extends Action
{
    /**
     * Replace a dashboard's widget placements with the given layout.
     *
     * Placements are addressed positionally rather than by id, so the caller
     * sends the layout it wants and the action reconciles.
     *
     * @param  array<int, array<string, mixed>>  $widgets
     */
    protected function handle(Dashboard $dashboard, array $widgets): Dashboard
    {
        DB::transaction(function () use ($dashboard, $widgets): void {
            $dashboard->widgets()->delete();

            foreach (array_values($widgets) as $index => $widget) {
                $dashboard->widgets()->create([
                    'widget_key' => $widget['widget_key'],
                    'grid_row' => $widget['grid_row'] ?? 0,
                    'grid_column' => $widget['grid_column'] ?? 0,
                    'grid_width' => $widget['grid_width'] ?? 4,
                    'grid_height' => $widget['grid_height'] ?? 2,
                    'settings' => $widget['settings'] ?? null,
                    'sort' => $index,
                ]);
            }

            $keys = array_map(
                fn (array $widget): string => (string) $widget['widget_key'],
                array_values($widgets),
            );

            DB::afterCommit(fn () => DashboardLayoutSavedActionEvent::dispatch($dashboard, $keys));
        });

        return $dashboard->refresh();
    }
}
