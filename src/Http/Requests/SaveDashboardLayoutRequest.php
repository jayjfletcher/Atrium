<?php

declare(strict_types=1);

namespace Atrium\Atrium\Http\Requests;

use Atrium\Atrium\Actions\SaveDashboardLayoutAction;
use Atrium\Atrium\Dashboards\DashboardManager;
use Atrium\Atrium\Models\Dashboard;
use Atrium\Atrium\Widgets\WidgetRegistry;
use Illuminate\Http\JsonResponse;

class SaveDashboardLayoutRequest extends Request
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
            'widgets' => ['array'],
            'widgets.*.widget_key' => ['required', 'string'],
            'widgets.*.grid_row' => ['integer', 'min:0'],
            'widgets.*.grid_column' => ['integer', 'min:0'],
            'widgets.*.grid_width' => ['integer', 'min:1'],
            'widgets.*.grid_height' => ['integer', 'min:1'],
            'widgets.*.settings' => ['array'],
        ];
    }

    public function persist(): JsonResponse
    {
        $dashboard = app(SaveDashboardLayoutAction::class)->execute(
            $this->dashboard(),
            $this->placements(),
        );

        return response()->json([
            'data' => ['saved' => $dashboard->widgets()->count()],
        ]);
    }

    /**
     * Drop placements for widgets this request may not place, so an
     * uninstalled or unauthorized widget cannot be persisted.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function placements(): array
    {
        $available = app(WidgetRegistry::class)->available($this);

        /** @var array<int, array<string, mixed>> $submitted */
        $submitted = $this->validated()['widgets'] ?? [];

        return array_values(array_filter(
            $submitted,
            fn (array $widget): bool => isset($available[$widget['widget_key']]),
        ));
    }

    protected function dashboard(): Dashboard
    {
        $dashboard = $this->route('dashboard');

        abort_unless($dashboard instanceof Dashboard, 404);

        return $dashboard;
    }
}
