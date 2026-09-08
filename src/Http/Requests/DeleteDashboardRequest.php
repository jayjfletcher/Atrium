<?php

declare(strict_types=1);

namespace Atrium\Atrium\Http\Requests;

use Atrium\Atrium\Actions\DeleteDashboardAction;
use Atrium\Atrium\Dashboards\DashboardManager;
use Atrium\Atrium\Models\Dashboard;
use Illuminate\Http\RedirectResponse;

class DeleteDashboardRequest extends Request
{
    public function authorize(): bool
    {
        return app(DashboardManager::class)->canModify($this, $this->dashboard());
    }

    public function persist(): RedirectResponse
    {
        app(DeleteDashboardAction::class)->execute($this->dashboard());

        return redirect()->route('atrium.dashboard');
    }

    protected function dashboard(): Dashboard
    {
        $dashboard = $this->route('dashboard');

        abort_unless($dashboard instanceof Dashboard, 404);

        return $dashboard;
    }
}
