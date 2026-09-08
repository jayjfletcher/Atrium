<?php

declare(strict_types=1);

namespace Atrium\Atrium\Events\DashboardWidget;

use Atrium\Atrium\Events\Event;
use Atrium\Atrium\Models\DashboardWidget;

/**
 * Dispatched on the DashboardWidget model's "updated" lifecycle hook.
 */
class DashboardWidgetUpdatedEvent extends Event
{
    public function __construct(public DashboardWidget $widget) {}
}
