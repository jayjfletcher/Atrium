<?php

declare(strict_types=1);

namespace JayI\Atrium\Http\Requests;

use Illuminate\Http\RedirectResponse;
use JayI\Atrium\Actions\DeleteDashboardAction;
use JayI\Atrium\Models\Dashboard;

class DeleteDashboardRequest extends Request
{
    public function authorize(): bool
    {
        return $this->allows('delete', $this->dashboard());
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
