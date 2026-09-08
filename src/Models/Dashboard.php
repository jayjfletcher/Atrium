<?php

declare(strict_types=1);

namespace Atrium\Atrium\Models;

use Atrium\Atrium\Events\Dashboard\DashboardCreatedEvent;
use Atrium\Atrium\Events\Dashboard\DashboardCreatingEvent;
use Atrium\Atrium\Events\Dashboard\DashboardDeletedEvent;
use Atrium\Atrium\Events\Dashboard\DashboardDeletingEvent;
use Atrium\Atrium\Events\Dashboard\DashboardReplicatingEvent;
use Atrium\Atrium\Events\Dashboard\DashboardRetrievedEvent;
use Atrium\Atrium\Events\Dashboard\DashboardSavedEvent;
use Atrium\Atrium\Events\Dashboard\DashboardSavingEvent;
use Atrium\Atrium\Events\Dashboard\DashboardUpdatedEvent;
use Atrium\Atrium\Events\Dashboard\DashboardUpdatingEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $owner_type
 * @property int|string|null $owner_id
 * @property bool $is_default
 * @property bool $is_shared
 * @property int $sort
 */
class Dashboard extends Model
{
    protected $table = 'atrium_dashboards';

    protected $guarded = [];

    protected $casts = [
        'is_default' => 'boolean',
        'is_shared' => 'boolean',
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
        'retrieved' => DashboardRetrievedEvent::class,
        'creating' => DashboardCreatingEvent::class,
        'created' => DashboardCreatedEvent::class,
        'updating' => DashboardUpdatingEvent::class,
        'updated' => DashboardUpdatedEvent::class,
        'saving' => DashboardSavingEvent::class,
        'saved' => DashboardSavedEvent::class,
        'deleting' => DashboardDeletingEvent::class,
        'deleted' => DashboardDeletedEvent::class,
        'replicating' => DashboardReplicatingEvent::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $dashboard): void {
            if (blank($dashboard->slug)) {
                $dashboard->slug = Str::slug($dashboard->name) ?: Str::random(8);
            }
        });
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany<DashboardWidget, $this>
     */
    public function widgets(): HasMany
    {
        return $this->hasMany(DashboardWidget::class, 'dashboard_id')->orderBy('sort');
    }

    /**
     * Dashboards this owner can see: their own, plus shared ones.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeVisibleTo(Builder $query, ?Model $owner): Builder
    {
        return $query->where(function (Builder $query) use ($owner): void {
            $query->where('is_shared', true);

            if ($owner !== null) {
                $query->orWhere(function (Builder $query) use ($owner): void {
                    $query->where('owner_type', $owner->getMorphClass())
                        ->where('owner_id', $owner->getKey());
                });
            }
        });
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOwnedBy(Builder $query, Model $owner): Builder
    {
        return $query->where('owner_type', $owner->getMorphClass())
            ->where('owner_id', $owner->getKey());
    }

    public function isOwnedBy(?Model $owner): bool
    {
        if ($owner === null || $this->owner_type === null) {
            return false;
        }

        return $this->owner_type === $owner->getMorphClass()
            && (string) $this->owner_id === (string) $owner->getKey();
    }
}
