<?php

declare(strict_types=1);

namespace JayI\Atrium\Events\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JayI\Atrium\Contracts\ModelLifecycleEvent;
use JayI\Atrium\Models\Dashboard;

/**
 * The Dashboard `deleting` Eloquent event.
 */
final class DashboardDeletingEvent implements ModelLifecycleEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Dashboard $dashboard) {}

    public function model(): Model
    {
        return $this->dashboard;
    }

    public function hook(): string
    {
        return 'deleting';
    }
}
