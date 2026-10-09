<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Dashboard\Http\Requests;

use Illuminate\Http\RedirectResponse;
use RefactorCircus\Atrium\Domains\Dashboard\Actions\UpdateDashboardAction;
use RefactorCircus\Atrium\Domains\Dashboard\Models\DashboardModel;
use RefactorCircus\Atrium\Http\Requests\Request;

class UpdateDashboardRequest extends Request
{
    public function authorize(): bool
    {
        return $this->allows('update', $this->dashboard());
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

    protected function dashboard(): DashboardModel
    {
        $dashboard = $this->route('dashboard');

        abort_unless($dashboard instanceof DashboardModel, 404);

        return $dashboard;
    }
}
