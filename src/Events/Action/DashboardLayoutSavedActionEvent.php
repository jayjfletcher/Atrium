<?php

declare(strict_types=1);

namespace JayI\Atrium\Events\Action;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JayI\Atrium\Contracts\ActionFinishedEvent;
use JayI\Atrium\Models\Dashboard;

/**
 * A dashboard's widget placements were replaced. Carries the widget keys as saved, in order.
 */
final class DashboardLayoutSavedActionEvent implements ActionFinishedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Dashboard $dashboard,
        /** @var array<int, string> */
        public array $widgetKeys,
    ) {}
}
