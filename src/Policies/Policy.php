<?php

declare(strict_types=1);

namespace JayI\Atrium\Policies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use JayI\Atrium\Models\Dashboard;

/**
 * Shared checks for the bundled policies.
 *
 * Each policy is registered from `atrium.policies`, so an application swaps
 * one by pointing its model at another class there.
 */
abstract class Policy
{
    /**
     * Whether the user is the dashboard's owner.
     */
    protected function owns(Model $user, Dashboard $dashboard): bool
    {
        return $dashboard->isOwnedBy($user);
    }

    /**
     * Ask the Gate about the parent dashboard, so a model that lives on a
     * dashboard follows whichever dashboard policy is registered.
     *
     * @param  array<int, mixed>  $arguments
     */
    protected function allowsOnDashboard(Model $user, string $ability, Dashboard $dashboard, array $arguments = []): bool
    {
        return Gate::forUser($user)->allows($ability, [$dashboard, ...$arguments]);
    }
}
