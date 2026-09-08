<?php

declare(strict_types=1);

namespace Atrium\Atrium\Http\Requests;

use Atrium\Atrium\Actions\UpdateDashboardAction;
use Atrium\Atrium\Dashboards\DashboardManager;
use Atrium\Atrium\Models\Dashboard;
use Illuminate\Http\RedirectResponse;

class UpdateDashboardRequest extends Request
{
    public function authorize(): bool
    {
        return app(DashboardManager::class)->canModify($this, $this->dashboard());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }

    public function persist(): RedirectResponse
    {
        $dashboard = app(UpdateDashboardAction::class)->execute(
            $this->dashboard(),
            $this->validated(),
        );

        return redirect()->route('atrium.dashboard.show', $dashboard->slug);
    }

    protected function dashboard(): Dashboard
    {
        $dashboard = $this->route('dashboard');

        abort_unless($dashboard instanceof Dashboard, 404);

        return $dashboard;
    }
}
