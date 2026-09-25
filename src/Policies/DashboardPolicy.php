<?php

declare(strict_types=1);

namespace JayI\Atrium\Policies;

use Illuminate\Database\Eloquent\Model;
use JayI\Atrium\Models\Dashboard;

/**
 * Answers `$user->can(...)` for dashboards.
 *
 * The dashboard's owner may do anything. Everyone else may view a shared
 * dashboard and nothing more: a shared dashboard has no owner, so nobody
 * edits it through Atrium. Point `atrium.policies` at your own class to
 * replace this one, for example to let administrators curate shared
 * dashboards.
 */
class DashboardPolicy extends Policy
{
    /**
     * Listings are already limited to the dashboards the user can see.
     */
    public function viewAny(Model $user): bool
    {
        return true;
    }

    public function create(Model $user): bool
    {
        return true;
    }

    public function view(Model $user, Dashboard $dashboard): bool
    {
        return $this->owns($user, $dashboard) || $dashboard->is_shared;
    }

    public function update(Model $user, Dashboard $dashboard): bool
    {
        return $this->owns($user, $dashboard);
    }

    public function delete(Model $user, Dashboard $dashboard): bool
    {
        return $this->owns($user, $dashboard);
    }
}
