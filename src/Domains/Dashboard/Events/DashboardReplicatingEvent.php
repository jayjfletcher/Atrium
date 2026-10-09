<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Dashboard\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use RefactorCircus\Atrium\Domains\Dashboard\Models\DashboardModel;
use RefactorCircus\Foundation\Contracts\ModelLifecycleEvent;

/**
 * The Dashboard `replicating` Eloquent event.
 */
final class DashboardReplicatingEvent implements ModelLifecycleEvent
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
        return 'replicating';
    }
}
