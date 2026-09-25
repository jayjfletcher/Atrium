<?php

declare(strict_types=1);

namespace JayI\Atrium\Events\Action;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JayI\Atrium\Contracts\ActionStartingEvent;
use JayI\Atrium\Models\Dashboard;

/**
 * A dashboard is about to be deleted.
 */
final class DashboardDeletingActionEvent implements ActionStartingEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Dashboard $dashboard,
    ) {}
}
