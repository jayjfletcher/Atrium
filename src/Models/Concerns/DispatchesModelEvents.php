<?php

declare(strict_types=1);

namespace JayI\Atrium\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Dispatch a class-based event for every Eloquent lifecycle hook.
 *
 * Each hook maps by convention to `JayI\Atrium\Events\Model\{Model}{Hook}Event`
 * — `DashboardWidget` fires `DashboardWidgetCreatingEvent` — and hooks without
 * such a class (soft-delete hooks on models that cannot be trashed) are skipped.
 *
 * A subclass of a package model, such as an application's `TeamDashboard
 * extends Dashboard`, fires the package model's events, so listeners see every
 * dashboard whatever class it hydrates as. Entries a model declares on
 * `$dispatchesEvents` itself win over the derived ones.
 */
trait DispatchesModelEvents
{
    /**
     * Every Eloquent hook, in the order Eloquent documents them.
     *
     * @var list<string>
     */
    private static array $modelEventHooks = [
        'retrieved', 'creating', 'created', 'updating', 'updated',
        'saving', 'saved', 'deleting', 'deleted', 'restoring', 'restored',
        'trashed', 'forceDeleting', 'forceDeleted', 'replicating',
    ];

    /**
     * @var array<class-string, array<string, class-string>>
     */
    private static array $derivedModelEvents = [];

    protected function initializeDispatchesModelEvents(): void
    {
        // At construction $dispatchesEvents holds only the class's declared
        // entries, so the merged map is the same for every instance.
        $this->dispatchesEvents = self::$derivedModelEvents[static::class]
            ??= array_merge(self::deriveModelEvents(), $this->dispatchesEvents);
    }

    /**
     * @return array<string, class-string>
     */
    private static function deriveModelEvents(): array
    {
        $prefix = class_basename(self::packageModel());
        $map = [];

        foreach (self::$modelEventHooks as $hook) {
            $event = 'JayI\\Atrium\\Events\\Model\\'.$prefix.Str::ucfirst($hook).'Event';

            if (class_exists($event)) {
                $map[$hook] = $event;
            }
        }

        return $map;
    }

    /**
     * The package model this class is, or extends.
     */
    private static function packageModel(): string
    {
        foreach ([static::class, ...array_values(class_parents(static::class) ?: [])] as $class) {
            if (Str::startsWith($class, 'JayI\\Atrium\\Models\\')) {
                return $class;
            }
        }

        return static::class;
    }
}
