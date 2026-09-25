<?php

declare(strict_types=1);

namespace JayI\Atrium\Http\Requests;

use Illuminate\Http\RedirectResponse;
use JayI\Atrium\Actions\UpdateDashboardAction;
use JayI\Atrium\Models\Dashboard;

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

    protected function dashboard(): Dashboard
    {
        $dashboard = $this->route('dashboard');

        abort_unless($dashboard instanceof Dashboard, 404);

        return $dashboard;
    }
}
