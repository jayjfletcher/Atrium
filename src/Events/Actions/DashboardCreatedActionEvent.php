<?php

declare(strict_types=1);

namespace Atrium\Atrium\Events\Actions;

use Atrium\Atrium\Events\Event;
use Atrium\Atrium\Models\Dashboard;

/**
 * Dispatched after the matching action commits.
 */
class DashboardCreatedActionEvent extends Event
{
    public function __construct(
        public Dashboard $dashboard,
    ) {}
}
