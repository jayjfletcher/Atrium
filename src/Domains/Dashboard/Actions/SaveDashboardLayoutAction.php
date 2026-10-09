<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Dashboard\Actions;

use Illuminate\Support\Facades\DB;
use RefactorCircus\Atrium\Domains\Dashboard\Events\DashboardLayoutSavedActionEvent;
use RefactorCircus\Atrium\Domains\Dashboard\Events\DashboardLayoutSavingActionEvent;
use RefactorCircus\Atrium\Domains\Dashboard\Models\DashboardModel;
use RefactorCircus\Keystone\Actions\Action;

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
    protected function handle(DashboardModel $dashboard, array $widgets): DashboardModel
    {
        DashboardLayoutSavingActionEvent::dispatch($dashboard, $widgets);

        $result = $this->perform($dashboard, $widgets);

        DashboardLayoutSavedActionEvent::dispatch($result, array_map(
            fn (array $widget): string => (string) $widget['widget_key'],
            array_values($widgets),
        ));

        return $result;
    }

    /**
     * @param  array<int, array<string, mixed>>  $widgets
     */
    private function perform(DashboardModel $dashboard, array $widgets): DashboardModel
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
        });

        return $dashboard->refresh();
    }
}
