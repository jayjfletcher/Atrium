<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Dashboard\Http\Requests;

use Illuminate\Http\RedirectResponse;
use RefactorCircus\Atrium\Domains\Dashboard\Actions\CreateDashboardAction;
use RefactorCircus\Atrium\Domains\Dashboard\Models\DashboardModel;
use RefactorCircus\Atrium\Domains\Dashboard\Services\DashboardManager;
use RefactorCircus\Atrium\Http\Requests\Request;

class StoreDashboardRequest extends Request
{
    public function authorize(): bool
    {
        return $this->allows('create', DashboardModel::class);
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
