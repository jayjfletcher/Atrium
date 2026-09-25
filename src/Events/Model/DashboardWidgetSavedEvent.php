<?php

declare(strict_types=1);

namespace JayI\Atrium\Events\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JayI\Atrium\Contracts\ModelLifecycleEvent;
use JayI\Atrium\Models\DashboardWidget;

/**
 * The DashboardWidget `saved` Eloquent event.
 */
final class DashboardWidgetSavedEvent implements ModelLifecycleEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public DashboardWidget $widget) {}

    public function model(): Model
    {
        return $this->widget;
    }

    public function hook(): string
    {
        return 'saved';
    }
}
