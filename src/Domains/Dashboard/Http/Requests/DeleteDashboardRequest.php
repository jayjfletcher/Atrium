<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Dashboard\Http\Requests;

use Illuminate\Http\RedirectResponse;
use RefactorCircus\Atrium\Domains\Dashboard\Actions\DeleteDashboardAction;
use RefactorCircus\Atrium\Domains\Dashboard\Models\DashboardModel;
use RefactorCircus\Atrium\Http\Requests\Request;

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

    protected function dashboard(): DashboardModel
    {
        $dashboard = $this->route('dashboard');

        abort_unless($dashboard instanceof DashboardModel, 404);

        return $dashboard;
    }
}
