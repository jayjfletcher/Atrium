<?php

declare(strict_types=1);

namespace JayI\Atrium\Tests\Fixtures\Policies;

use Illuminate\Database\Eloquent\Model;
use JayI\Atrium\Models\Dashboard;
use JayI\Atrium\Policies\DashboardPolicy;

/**
 * Nobody may change a dashboard, not even its owner.
 */
final class ReadOnlyDashboardPolicy extends DashboardPolicy
{
    public function create(Model $user): bool
    {
        return false;
    }

    public function update(Model $user, Dashboard $dashboard): bool
    {
        return false;
    }
}
