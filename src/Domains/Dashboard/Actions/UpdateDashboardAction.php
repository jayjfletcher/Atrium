<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Dashboard\Actions;

use Illuminate\Support\Facades\DB;
use RefactorCircus\Atrium\Domains\Dashboard\Events\DashboardUpdatedActionEvent;
use RefactorCircus\Atrium\Domains\Dashboard\Events\DashboardUpdatingActionEvent;
use RefactorCircus\Atrium\Domains\Dashboard\Models\DashboardModel;
use RefactorCircus\Foundation\Actions\Action;

class UpdateDashboardAction extends Action
{
    /**
     * @param  array{name?: string, is_default?: bool}  $data
     */
    protected function handle(DashboardModel $dashboard, array $data): DashboardModel
    {
        DashboardUpdatingActionEvent::dispatch($dashboard, $data);

        $result = $this->perform($dashboard, $data);

        DashboardUpdatedActionEvent::dispatch($result);

        return $result;
    }

    /**
     * @param  array{name?: string, is_default?: bool}  $data
     */
    private function perform(DashboardModel $dashboard, array $data): DashboardModel
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
