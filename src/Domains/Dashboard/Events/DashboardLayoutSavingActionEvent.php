<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Dashboard\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JayI\Atrium\Domains\Dashboard\Models\DashboardModel;
use JayI\Foundation\Contracts\ActionStartingEvent;

/**
 * A dashboard's widget placements are about to be replaced.
 */
final class DashboardLayoutSavingActionEvent implements ActionStartingEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public DashboardModel $dashboard,
        /** @var array<int, array<string, mixed>> */
        public array $widgets,
    ) {}
}
