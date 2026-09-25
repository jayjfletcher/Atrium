<?php

declare(strict_types=1);

namespace JayI\Atrium\Http\Requests;

use Illuminate\Http\JsonResponse;
use JayI\Atrium\Actions\SaveDashboardLayoutAction;
use JayI\Atrium\Models\Dashboard;
use JayI\Atrium\Models\DashboardWidget;
use JayI\Atrium\Widgets\WidgetRegistry;

class SaveDashboardLayoutRequest extends Request
{
    /**
     * Saving replaces every placement: it needs `update` on the dashboard,
     * `delete` on each placement it removes, and `create` on placements when
     * it adds any.
     */
    public function authorize(): bool
    {
        $dashboard = $this->dashboard();
        $submitted = $this->input('widgets');

        return $this->allows('update', $dashboard)
            && $this->allowsEach('delete', $dashboard->widgets()->get())
            && (! is_array($submitted) || $submitted === [] || $this->allows('create', DashboardWidget::class, [$dashboard]));
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
