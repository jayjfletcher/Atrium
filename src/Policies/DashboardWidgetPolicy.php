<?php

declare(strict_types=1);

namespace JayI\Atrium\Policies;

use Illuminate\Database\Eloquent\Model;
use JayI\Atrium\Models\Dashboard;
use JayI\Atrium\Models\DashboardWidget;

/**
 * Widget placements are dashboard content, so each check defers to the
 * dashboard: reading a placement needs `view` on its dashboard, changing one
 * needs `update`.
 */
class DashboardWidgetPolicy extends Policy
{
    public function viewAny(Model $user, Dashboard $dashboard): bool
    {
        return $this->allowsOnDashboard($user, 'view', $dashboard);
    }

    public function view(Model $user, DashboardWidget $widget): bool
    {
        return $this->allowsOnDashboard($user, 'view', $widget->dashboard);
    }

    public function create(Model $user, Dashboard $dashboard): bool
    {
        return $this->allowsOnDashboard($user, 'update', $dashboard);
    }

    public function update(Model $user, DashboardWidget $widget): bool
    {
        return $this->allowsOnDashboard($user, 'update', $widget->dashboard);
    }

    public function delete(Model $user, DashboardWidget $widget): bool
    {
        return $this->allowsOnDashboard($user, 'update', $widget->dashboard);
    }
}
