<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Dashboard\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JayI\Atrium\Domains\Dashboard\Models\DashboardWidgetModel;
use JayI\Foundation\Contracts\ModelLifecycleEvent;

/**
 * The DashboardWidget `retrieved` Eloquent event.
 */
final class DashboardWidgetRetrievedEvent implements ModelLifecycleEvent
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
        return 'retrieved';
    }
}
