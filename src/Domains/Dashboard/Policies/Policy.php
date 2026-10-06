<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Dashboard\Policies;

use Illuminate\Database\Eloquent\Model;
use JayI\Atrium\Domains\Dashboard\Models\DashboardModel;
use JayI\Foundation\Policies\Policy as BasePolicy;

/**
 * Shared checks for the bundled policies.
 *
 * Each policy is registered from `atrium.policies`, so an application swaps
 * one by pointing its model at another class there.
 */
abstract class Policy extends BasePolicy
{
    /**
     * Whether the user is the dashboard's owner.
     */
    protected function owns(Model $user, DashboardModel $dashboard): bool
    {
        return $dashboard->isOwnedBy($user);
    }

    /**
     * Ask the Gate about the parent dashboard, so a model that lives on a
     * dashboard follows whichever dashboard policy is registered.
     *
     * @param  array<int, mixed>  $arguments
     */
    protected function allowsOnDashboard(Model $user, string $ability, DashboardModel $dashboard, array $arguments = []): bool
    {
        return $this->allowsOn($user, $ability, $dashboard, $arguments);
    }
}
