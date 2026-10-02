<?php

declare(strict_types=1);

namespace JayI\Atrium\Access;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Answers the two questions that decide what a request may see: does the
 * user hold a permission, and is a feature on. Atrium ships no opinion on
 * either beyond Laravel's Gate, so applications and packages (such as
 * jayi/pennantplus) swap in their own answers.
 */
class Gatekeeper
{
    /** @var (Closure(string, array<array-key, mixed>, Request): bool)|null */
    private ?Closure $permissions = null;

    /** @var (Closure(string, Request): bool)|null */
    private ?Closure $features = null;

    /**
     * Decide permissions with the given callback instead of the Gate.
     *
     * @param  (Closure(string $ability, array<array-key, mixed> $arguments, Request $request): bool)|null  $callback
     */
    public function resolvePermissionsUsing(?Closure $callback): static
    {
        $this->permissions = $callback;

        return $this;
    }

    /**
     * Decide whether features are on with the given callback. Until one is
     * registered, every feature is on, so gating by feature is opt-in.
     *
     * @param  (Closure(string $feature, Request $request): bool)|null  $callback
     */
    public function resolveFeaturesUsing(?Closure $callback): static
    {
        $this->features = $callback;

        return $this;
    }

    /**
     * @param  array<array-key, mixed>  $arguments
     */
    public function allows(string $ability, array $arguments, Request $request): bool
    {
        if ($this->permissions !== null) {
            return ($this->permissions)($ability, $arguments, $request) === true;
        }

        return Gate::forUser($request->user())->allows($ability, $arguments);
    }

    public function enabled(string $feature, Request $request): bool
    {
        return $this->features === null || ($this->features)($feature, $request) === true;
    }
}
