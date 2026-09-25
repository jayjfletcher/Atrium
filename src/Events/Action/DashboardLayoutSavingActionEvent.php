<?php

declare(strict_types=1);

namespace JayI\Atrium\Events\Action;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JayI\Atrium\Contracts\ActionStartingEvent;
use JayI\Atrium\Models\Dashboard;

/**
 * A dashboard's widget placements are about to be replaced.
 */
final class DashboardLayoutSavingActionEvent implements ActionStartingEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Dashboard $dashboard,
        /** @var array<int, array<string, mixed>> */
        public array $widgets,
    ) {}
}
