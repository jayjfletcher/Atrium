<?php

declare(strict_types=1);

namespace Atrium\Atrium\Actions;

use Atrium\Atrium\Events\Actions\DashboardCreatedActionEvent;
use Atrium\Atrium\Models\Dashboard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateDashboardAction extends Action
{
    /**
     * @param  array{name: string, is_shared?: bool}  $data
     */
    protected function handle(array $data, ?Model $owner = null): Dashboard
    {
        return DB::transaction(function () use ($data, $owner): Dashboard {
            $shared = (bool) ($data['is_shared'] ?? false);

            $dashboard = Dashboard::query()->create([
                'name' => $data['name'],
                'owner_type' => $shared ? null : $owner?->getMorphClass(),
                'owner_id' => $shared ? null : $owner?->getKey(),
                'is_shared' => $shared,
            ]);

            DB::afterCommit(fn () => DashboardCreatedActionEvent::dispatch($dashboard));

            return $dashboard;
        });
    }
}
