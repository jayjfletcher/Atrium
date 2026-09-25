<?php

declare(strict_types=1);

namespace JayI\Atrium\Events\Action;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JayI\Atrium\Contracts\ActionFinishedEvent;
use JayI\Atrium\Models\Dashboard;

/**
 * A dashboard and its widget placements were deleted.
 */
final class DashboardDeletedActionEvent implements ActionFinishedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Dashboard $dashboard,
    ) {}
}
