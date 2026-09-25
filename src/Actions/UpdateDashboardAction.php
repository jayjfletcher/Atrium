<?php

declare(strict_types=1);

namespace JayI\Atrium\Actions;

use Illuminate\Support\Facades\DB;
use JayI\Atrium\Events\Action\DashboardUpdatedActionEvent;
use JayI\Atrium\Events\Action\DashboardUpdatingActionEvent;
use JayI\Atrium\Models\Dashboard;

class UpdateDashboardAction extends Action
{
    /**
     * @param  array{name?: string, is_default?: bool}  $data
     */
    protected function handle(Dashboard $dashboard, array $data): Dashboard
    {
        DashboardUpdatingActionEvent::dispatch($dashboard, $data);

        $result = $this->perform($dashboard, $data);

        DashboardUpdatedActionEvent::dispatch($result);

        return $result;
    }

    /**
     * @param  array{name?: string, is_default?: bool}  $data
     */
    private function perform(Dashboard $dashboard, array $data): Dashboard
    {
        DB::transaction(function () use ($dashboard, $data): void {
            $dashboard->update(array_filter(
                [
                    'name' => $data['name'] ?? null,
                    'is_default' => $data['is_default'] ?? null,
                ],
                fn (mixed $value): bool => $value !== null,
            ));
        });

        return $dashboard->refresh();
    }
}
