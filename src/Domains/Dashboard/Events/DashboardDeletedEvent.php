<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Dashboard\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JayI\Atrium\Domains\Dashboard\Models\DashboardModel;
use JayI\Foundation\Contracts\ModelLifecycleEvent;

/**
 * The Dashboard `deleted` Eloquent event.
 */
final class DashboardDeletedEvent implements ModelLifecycleEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public DashboardModel $dashboard) {}

    public function model(): Model
    {
        return $this->dashboard;
    }

    public function hook(): string
    {
        return 'deleted';
    }
}
