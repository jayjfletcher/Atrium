<?php

declare(strict_types=1);

namespace Atrium\Atrium\Events\Actions;

use Atrium\Atrium\Events\Event;
use Atrium\Atrium\Models\Dashboard;

/**
 * Dispatched after the matching action commits.
 * Carries the widget keys as saved, in order.
 */
class DashboardLayoutSavedActionEvent extends Event
{
    public function __construct(
        public Dashboard $dashboard,
        /** @var array<int, string> */
        public array $widgetKeys,
    ) {}
}
