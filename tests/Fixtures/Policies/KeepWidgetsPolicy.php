<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Tests\Fixtures\Policies;

use Illuminate\Database\Eloquent\Model;
use RefactorCircus\Atrium\Domains\Dashboard\Models\DashboardWidgetModel;
use RefactorCircus\Atrium\Domains\Dashboard\Policies\DashboardWidgetPolicy;

/**
 * Widget placements may be added but never removed.
 */
final class KeepWidgetsPolicy extends DashboardWidgetPolicy
{
    public function delete(Model $user, DashboardWidgetModel $widget): bool
    {
        return false;
    }
}
