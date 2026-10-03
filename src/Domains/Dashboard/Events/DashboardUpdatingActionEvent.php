<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Dashboard\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JayI\Atrium\Contracts\ActionStartingEvent;
use JayI\Atrium\Domains\Dashboard\Models\DashboardModel;

/**
 * A dashboard is about to be renamed or made the default.
 */
final class DashboardUpdatingActionEvent implements ActionStartingEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public DashboardModel $dashboard,
        /** @var array{name?: string, is_default?: bool} */
        public array $data,
    ) {}
}
