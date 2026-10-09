<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Dashboard\Actions;

use Illuminate\Support\Facades\DB;
use RefactorCircus\Atrium\Domains\Dashboard\Events\DashboardDeletedActionEvent;
use RefactorCircus\Atrium\Domains\Dashboard\Events\DashboardDeletingActionEvent;
use RefactorCircus\Atrium\Domains\Dashboard\Models\DashboardModel;
use RefactorCircus\Keystone\Actions\Action;

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
