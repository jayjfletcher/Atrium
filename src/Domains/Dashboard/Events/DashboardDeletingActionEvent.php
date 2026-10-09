<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Dashboard\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use RefactorCircus\Atrium\Domains\Dashboard\Models\DashboardModel;
use RefactorCircus\Keystone\Contracts\ActionStartingEvent;

/**
 * A dashboard is about to be deleted.
 */
final class DashboardDeletingActionEvent implements ActionStartingEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public DashboardModel $dashboard,
    ) {}
}
