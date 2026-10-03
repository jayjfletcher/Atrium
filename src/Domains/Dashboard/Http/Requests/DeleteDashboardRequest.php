<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Dashboard\Http\Requests;

use Illuminate\Http\RedirectResponse;
use JayI\Atrium\Domains\Dashboard\Actions\DeleteDashboardAction;
use JayI\Atrium\Domains\Dashboard\Models\DashboardModel;
use JayI\Atrium\Http\Requests\Request;

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
