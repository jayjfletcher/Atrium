<?php

declare(strict_types=1);

namespace Atrium\Atrium\Actions;

use Atrium\Atrium\Events\Actions\DashboardUpdatedActionEvent;
use Atrium\Atrium\Models\Dashboard;
use Illuminate\Support\Facades\DB;

class UpdateDashboardAction extends Action
{
    /**
     * @param  array{name?: string, is_default?: bool}  $data
     */
    protected function handle(Dashboard $dashboard, array $data): Dashboard
    {
        DB::transaction(function () use ($dashboard, $data): void {
            $dashboard->update(array_filter(
                [
                    'name' => $data['name'] ?? null,
                    'is_default' => $data['is_default'] ?? null,
                ],
                fn (mixed $value): bool => $value !== null,
            ));

            DB::afterCommit(fn () => DashboardUpdatedActionEvent::dispatch($dashboard));
        });

        return $dashboard->refresh();
    }
}
