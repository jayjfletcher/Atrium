<?php

declare(strict_types=1);

namespace Atrium\Atrium\Actions;

use Atrium\Atrium\Events\Actions\DashboardDeletedActionEvent;
use Atrium\Atrium\Models\Dashboard;
use Illuminate\Support\Facades\DB;

class DeleteDashboardAction extends Action
{
    protected function handle(Dashboard $dashboard): void
    {
        DB::transaction(function () use ($dashboard): void {
            $dashboard->delete();

            DB::afterCommit(fn () => DashboardDeletedActionEvent::dispatch($dashboard));
        });
    }
}
