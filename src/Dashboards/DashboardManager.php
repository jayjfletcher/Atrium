<?php

declare(strict_types=1);

namespace Atrium\Atrium\Dashboards;

use Atrium\Atrium\Models\Dashboard;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class DashboardManager
{
    /**
     * Dashboards the request's user may view: their own, plus shared ones.
     *
     * @return Collection<int, Dashboard>
     */
    public function visible(Request $request): Collection
    {
        return Dashboard::query()
            ->visibleTo($this->owner($request))
            ->orderBy('sort')
            ->orderBy('name')
            ->get();
    }

    /**
     * The dashboard to show when none is specified.
     */
    public function current(Request $request, ?string $slug = null): ?Dashboard
    {
        $dashboards = $this->visible($request);

        if ($slug !== null) {
            return $dashboards->firstWhere('slug', $slug);
        }

        return $dashboards->firstWhere('is_default', true) ?? $dashboards->first();
    }

    /**
     * Whether the request's user may modify the given dashboard.
     */
    public function canModify(Request $request, Dashboard $dashboard): bool
    {
        return $dashboard->isOwnedBy($this->owner($request));
    }

    /**
     * The model whose dashboards a request operates on, when the request has
     * an authenticated user backed by a model.
     */
    public function owner(Request $request): ?Model
    {
        $user = $request->user();

        return $user instanceof Model ? $user : null;
    }
}
