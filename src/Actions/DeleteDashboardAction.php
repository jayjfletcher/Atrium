<?php

declare(strict_types=1);

namespace JayI\Atrium\Actions;

use Illuminate\Support\Facades\DB;
use JayI\Atrium\Events\Action\DashboardDeletedActionEvent;
use JayI\Atrium\Events\Action\DashboardDeletingActionEvent;
use JayI\Atrium\Models\Dashboard;

class DeleteDashboardAction extends Action
{
    protected function handle(Dashboard $dashboard): void
    {
        DashboardDeletingActionEvent::dispatch($dashboard);

        $this->perform($dashboard);

        DashboardDeletedActionEvent::dispatch($dashboard);
    }

    private function perform(Dashboard $dashboard): void
    {
        DB::transaction(function () use ($dashboard): void {
            $dashboard->delete();
        });
    }
}
