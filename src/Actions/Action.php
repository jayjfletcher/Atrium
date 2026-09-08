<?php

declare(strict_types=1);

namespace Atrium\Atrium\Actions;

/**
 * Base class for Atrium's mutating business logic.
 *
 * Callers invoke `execute()`; concrete actions implement a `protected handle()`
 * with their own typed signature. The split gives every action one uniform
 * entry point and keeps a place to hook cross-cutting behavior without
 * touching each action.
 *
 * Resolve and invoke through the container rather than a static constructor:
 *
 *     app(CreateDashboardAction::class)->execute($user, $data);
 */
abstract class Action
{
    /**
     * Execute the action.
     *
     * Concrete actions implement `handle()` with a concrete return type; it is
     * invoked here so subclasses can wrap execution in one place.
     */
    public function execute(mixed ...$arguments): mixed
    {
        return $this->handle(...$arguments); // @phpstan-ignore method.notFound
    }
}
