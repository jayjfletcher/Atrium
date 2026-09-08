<?php

declare(strict_types=1);

namespace Atrium\Atrium\Http\Controllers;

use Atrium\Atrium\Actions\CreateDashboardAction;
use Atrium\Atrium\Dashboards\DashboardManager;
use Atrium\Atrium\Widgets\WidgetRegistry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController
{
    public function __construct(
        protected DashboardManager $dashboards,
        protected WidgetRegistry $widgets,
    ) {}

    public function index(Request $request): View
    {
        return $this->render($request, null);
    }

    public function show(Request $request, string $dashboard): View
    {
        return $this->render($request, $dashboard);
    }

    protected function render(Request $request, ?string $slug): View
    {
        $current = $this->dashboards->current($request, $slug);

        abort_if($slug !== null && $current === null, 404);

        // A signed-in user with no dashboard yet gets one, so the widget
        // picker is reachable on a first visit rather than dead-ending on an
        // empty state. The dashboard is created empty; no widget is placed.
        if ($current === null && $request->user() !== null) {
            $current = app(CreateDashboardAction::class)->execute(
                ['name' => __('atrium::atrium.dashboard')],
                $this->dashboards->owner($request),
            );
        }

        /** @var view-string $view */
        $view = 'atrium::dashboard.index';

        return view($view, [
            'dashboards' => $this->dashboards->visible($request),
            'dashboard' => $current,
            'placements' => $current === null ? collect() : $current->widgets,
            'available' => $this->widgets->available($request),
            'editable' => $current !== null && $this->dashboards->canModify($request, $current),
        ]);
    }
}
