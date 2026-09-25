<?php

declare(strict_types=1);

namespace JayI\Atrium\Tests\Fixtures\Policies;

use Illuminate\Database\Eloquent\Model;
use JayI\Atrium\Models\DashboardWidget;
use JayI\Atrium\Policies\DashboardWidgetPolicy;

/**
 * Widget placements may be added but never removed.
 */
final class KeepWidgetsPolicy extends DashboardWidgetPolicy
{
    public function delete(Model $user, DashboardWidget $widget): bool
    {
        return false;
    }
}
