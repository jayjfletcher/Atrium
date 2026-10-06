<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Dashboard\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JayI\Atrium\Domains\Dashboard\Models\DashboardModel;
use JayI\Foundation\Contracts\ActionFinishedEvent;

/**
 * A dashboard's widget placements were replaced. Carries the widget keys as saved, in order.
 */
final class DashboardLayoutSavedActionEvent implements ActionFinishedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public DashboardModel $dashboard,
        /** @var array<int, string> */
        public array $widgetKeys,
    ) {}
}
