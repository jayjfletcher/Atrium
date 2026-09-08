<?php

declare(strict_types=1);

namespace Atrium\Atrium\Http\Requests;

use Atrium\Atrium\Actions\CreateDashboardAction;
use Atrium\Atrium\Dashboards\DashboardManager;
use Illuminate\Http\RedirectResponse;

class StoreDashboardRequest extends Request
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function persist(): RedirectResponse
    {
        $dashboard = app(CreateDashboardAction::class)->execute(
            $this->validated(),
            app(DashboardManager::class)->owner($this),
        );

        return redirect()->route('atrium.dashboard.show', $dashboard->slug);
    }
}
