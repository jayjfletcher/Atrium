<?php

declare(strict_types=1);

namespace Atrium\Atrium\Events\Dashboard;

use Atrium\Atrium\Events\Event;
use Atrium\Atrium\Models\Dashboard;

/**
 * Dispatched on the Dashboard model's "deleting" lifecycle hook.
 */
class DashboardDeletingEvent extends Event
{
    public function __construct(public Dashboard $dashboard) {}
}
