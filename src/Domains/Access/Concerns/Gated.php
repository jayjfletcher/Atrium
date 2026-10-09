<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Access\Concerns;

use Closure;
use Illuminate\Http\Request;
use RefactorCircus\Atrium\Domains\Access\Services\Gatekeeper;

/**
 * Visibility rules shared by navigation items and groups. Every rule must
 * pass: each permission, each feature, and the authorize callback.
 */
trait Gated
{
    /** @var array<string, array<array-key, mixed>> Abilities mapped to their Gate arguments. */
    public private(set) array $abilities = [];

    /** @var array<int, string> */
    public private(set) array $features = [];

    /** @var (Closure(Request): bool)|null */
    private ?Closure $authorize = null;

    /**
     * Only show this to users holding the permission.
     *
     * @param  array<array-key, mixed>|mixed  $arguments
     */
    public function can(string $ability, mixed $arguments = []): static
    {
        $this->abilities[$ability] = is_array($arguments) ? $arguments : [$arguments];

        return $this;
    }

    /**
     * Only show this while every given feature is on.
     */
    public function feature(string ...$features): static
    {
        $this->features = array_values(array_unique([...$this->features, ...$features]));

        return $this;
    }

    /**
     * @param  Closure(Request): bool  $callback
     */
    public function authorize(Closure $callback): static
    {
        $this->authorize = $callback;

        return $this;
    }

    public function isAuthorized(Request $request): bool
    {
        $gatekeeper = app(Gatekeeper::class);

        foreach ($this->features as $feature) {
            if (! $gatekeeper->enabled($feature, $request)) {
                return false;
            }
        }

        foreach ($this->abilities as $ability => $arguments) {
            if (! $gatekeeper->allows($ability, $arguments, $request)) {
                return false;
            }
        }

        return $this->authorize === null || ($this->authorize)($request) === true;
    }
}
