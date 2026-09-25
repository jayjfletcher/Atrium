<?php

declare(strict_types=1);

namespace JayI\Atrium\Http\Requests;

use Illuminate\Http\RedirectResponse;
use JayI\Atrium\Actions\CreateDashboardAction;
use JayI\Atrium\Dashboards\DashboardManager;
use JayI\Atrium\Models\Dashboard;

class StoreDashboardRequest extends Request
{
    public function authorize(): bool
    {
        return $this->allows('create', Dashboard::class);
    }

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
