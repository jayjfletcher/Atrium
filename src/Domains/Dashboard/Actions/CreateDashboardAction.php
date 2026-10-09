<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Dashboard\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RefactorCircus\Atrium\Domains\Dashboard\Events\DashboardCreatedActionEvent;
use RefactorCircus\Atrium\Domains\Dashboard\Events\DashboardCreatingActionEvent;
use RefactorCircus\Atrium\Domains\Dashboard\Models\DashboardModel;
use RefactorCircus\Keystone\Actions\Action;

class CreateDashboardAction extends Action
{
    /**
     * @param  array{name: string, is_shared?: bool}  $data
     */
    protected function handle(array $data, ?Model $owner = null): DashboardModel
    {
        DashboardCreatingActionEvent::dispatch($data, $owner);

        $dashboard = $this->perform($data, $owner);

        DashboardCreatedActionEvent::dispatch($dashboard);

        return $dashboard;
    }

    /**
     * @param  array{name: string, is_shared?: bool}  $data
     */
    private function perform(array $data, ?Model $owner): DashboardModel
    {
        return DB::transaction(function () use ($data, $owner): DashboardModel {
            $shared = (bool) ($data['is_shared'] ?? false);

            return DashboardModel::query()->create([
                'name' => $data['name'],
                'owner_type' => $shared ? null : $owner?->getMorphClass(),
                'owner_id' => $shared ? null : $owner?->getKey(),
                'is_shared' => $shared,
            ]);
        });
    }
}
