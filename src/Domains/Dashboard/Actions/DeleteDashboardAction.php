<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Dashboard\Actions;

use Illuminate\Support\Facades\DB;
use JayI\Atrium\Actions\Action;
use JayI\Atrium\Domains\Dashboard\Events\DashboardDeletedActionEvent;
use JayI\Atrium\Domains\Dashboard\Events\DashboardDeletingActionEvent;
use JayI\Atrium\Domains\Dashboard\Models\DashboardModel;

class DeleteDashboardAction extends Action
{
    protected function handle(DashboardModel $dashboard): void
    {
        DashboardDeletingActionEvent::dispatch($dashboard);

        $this->perform($dashboard);

        DashboardDeletedActionEvent::dispatch($dashboard);
    }

    private function perform(DashboardModel $dashboard): void
    {
        DB::transaction(function () use ($dashboard): void {
            $dashboard->delete();
        });
    }
}
