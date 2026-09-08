<?php

declare(strict_types=1);

namespace Atrium\Atrium\Models;

use Atrium\Atrium\Events\DashboardWidget\DashboardWidgetCreatedEvent;
use Atrium\Atrium\Events\DashboardWidget\DashboardWidgetCreatingEvent;
use Atrium\Atrium\Events\DashboardWidget\DashboardWidgetDeletedEvent;
use Atrium\Atrium\Events\DashboardWidget\DashboardWidgetDeletingEvent;
use Atrium\Atrium\Events\DashboardWidget\DashboardWidgetReplicatingEvent;
use Atrium\Atrium\Events\DashboardWidget\DashboardWidgetRetrievedEvent;
use Atrium\Atrium\Events\DashboardWidget\DashboardWidgetSavedEvent;
use Atrium\Atrium\Events\DashboardWidget\DashboardWidgetSavingEvent;
use Atrium\Atrium\Events\DashboardWidget\DashboardWidgetUpdatedEvent;
use Atrium\Atrium\Events\DashboardWidget\DashboardWidgetUpdatingEvent;
use Atrium\Atrium\Facades\Atrium;
use Atrium\Atrium\Widgets\WidgetDefinition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $dashboard_id
 * @property string $widget_key
 * @property int $grid_row
 * @property int $grid_column
 * @property int $grid_width
 * @property int $grid_height
 * @property int $sort
 * @property array<string, mixed>|null $settings
 */
class DashboardWidget extends Model
{
    protected $table = 'atrium_dashboard_widgets';

    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
        'grid_row' => 'integer',
        'grid_column' => 'integer',
        'grid_width' => 'integer',
        'grid_height' => 'integer',
        'sort' => 'integer',
    ];

    /**
     * Every Eloquent lifecycle hook maps to a typed event, so host apps and
     * plugins can listen for data-level concerns without patching the model.
     *
     * These are distinct from Atrium's action events: lifecycle events fire on
     * any write, action events carry business context.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'retrieved' => DashboardWidgetRetrievedEvent::class,
        'creating' => DashboardWidgetCreatingEvent::class,
        'created' => DashboardWidgetCreatedEvent::class,
        'updating' => DashboardWidgetUpdatingEvent::class,
        'updated' => DashboardWidgetUpdatedEvent::class,
        'saving' => DashboardWidgetSavingEvent::class,
        'saved' => DashboardWidgetSavedEvent::class,
        'deleting' => DashboardWidgetDeletingEvent::class,
        'deleted' => DashboardWidgetDeletedEvent::class,
        'replicating' => DashboardWidgetReplicatingEvent::class,
    ];

    /**
     * @return BelongsTo<Dashboard, $this>
     */
    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(Dashboard::class, 'dashboard_id');
    }

    /**
     * The registered definition for this placement.
     *
     * Returns null when the plugin that provided the widget is no longer
     * installed, so a removed plugin cannot break an existing dashboard.
     */
    public function definition(): ?WidgetDefinition
    {
        return Atrium::widgetRegistry()->get($this->widget_key);
    }

    public function isOrphaned(): bool
    {
        return $this->definition() === null;
    }
}
