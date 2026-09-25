<?php

declare(strict_types=1);

namespace JayI\Atrium\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use JayI\Atrium\Events\Action\DashboardCreatedActionEvent;
use JayI\Atrium\Events\Action\DashboardCreatingActionEvent;
use JayI\Atrium\Models\Dashboard;

class CreateDashboardAction extends Action
{
    /**
     * @param  array{name: string, is_shared?: bool}  $data
     */
    protected function handle(array $data, ?Model $owner = null): Dashboard
    {
        DashboardCreatingActionEvent::dispatch($data, $owner);

        $dashboard = $this->perform($data, $owner);

        DashboardCreatedActionEvent::dispatch($dashboard);

        return $dashboard;
    }

    /**
     * @param  array{name: string, is_shared?: bool}  $data
     */
    private function perform(array $data, ?Model $owner): Dashboard
    {
        return DB::transaction(function () use ($data, $owner): Dashboard {
            $shared = (bool) ($data['is_shared'] ?? false);

            return Dashboard::query()->create([
                'name' => $data['name'],
                'owner_type' => $shared ? null : $owner?->getMorphClass(),
                'owner_id' => $shared ? null : $owner?->getKey(),
                'is_shared' => $shared,
            ]);
        });
    }
}
