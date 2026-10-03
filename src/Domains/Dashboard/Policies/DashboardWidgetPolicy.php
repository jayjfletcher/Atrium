<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Dashboard\Policies;

use Illuminate\Database\Eloquent\Model;
use JayI\Atrium\Domains\Dashboard\Models\DashboardModel;
use JayI\Atrium\Domains\Dashboard\Models\DashboardWidgetModel;

/**
 * Widget placements are dashboard content, so each check defers to the
 * dashboard: reading a placement needs `view` on its dashboard, changing one
 * needs `update`.
 */
class DashboardWidgetPolicy extends Policy
{
    public function viewAny(Model $user, DashboardModel $dashboard): bool
    {
        return $this->allowsOnDashboard($user, 'view', $dashboard);
    }

    public function view(Model $user, DashboardWidgetModel $widget): bool
    {
        return $this->allowsOnDashboard($user, 'view', $widget->dashboard);
    }

    public function create(Model $user, DashboardModel $dashboard): bool
    {
        return $this->allowsOnDashboard($user, 'update', $dashboard);
    }

    public function update(Model $user, DashboardWidgetModel $widget): bool
    {
        return $this->allowsOnDashboard($user, 'update', $widget->dashboard);
    }

    public function delete(Model $user, DashboardWidgetModel $widget): bool
    {
        return $this->allowsOnDashboard($user, 'update', $widget->dashboard);
    }
}
