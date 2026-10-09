<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Dashboard\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use RefactorCircus\Atrium\Domains\Dashboard\Models\DashboardWidgetModel;
use RefactorCircus\Keystone\Contracts\ModelLifecycleEvent;

/**
 * The DashboardWidget `replicating` Eloquent event.
 */
final class DashboardWidgetReplicatingEvent implements ModelLifecycleEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public DashboardWidgetModel $widget) {}

    public function model(): Model
    {
        return $this->widget;
    }

    public function hook(): string
    {
        return 'replicating';
    }
}
