<?php

declare(strict_types=1);

namespace Atrium\Atrium\Navigation;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class NavItem
{
    public private(set) ?string $url = null;

    /** The route name this item points at, when it was built from a route. */
    public private(set) ?string $route = null;

    /** @var array<string, mixed> */
    private array $routeParameters = [];

    public private(set) ?string $icon = null;

    public private(set) ?string $group = null;

    public private(set) int $sort = 100;

    /** @var (Closure(): (string|int|null))|null */
    private ?Closure $badge = null;

    /** @var (Closure(Request): bool)|null */
    private ?Closure $authorize = null;

    /** @var array<int, NavItem> */
    public private(set) array $children = [];

    final public function __construct(public readonly string $label) {}

    public static function make(string $label): static
    {
        return new static($label);
    }

    public function url(string $url): static
    {
        $this->url = $url;
        $this->route = null;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function route(string $name, array $parameters = []): static
    {
        $this->route = $name;
        $this->routeParameters = $parameters;
        $this->url = null;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function group(string $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function sort(int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @param  Closure(): (string|int|null)  $badge
     */
    public function badge(Closure $badge): static
    {
        $this->badge = $badge;

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

    /**
     * @param  array<int, NavItem>  $children
     */
    public function children(array $children): static
    {
        $this->children = $children;

        return $this;
    }

    public function resolveUrl(): ?string
    {
        if ($this->url !== null) {
            return $this->url;
        }

        if ($this->route === null || ! Route::has($this->route)) {
            return null;
        }

        return route($this->route, $this->routeParameters);
    }

    public function resolveBadge(): string|int|null
    {
        return $this->badge === null ? null : ($this->badge)();
    }

    public function isAuthorized(Request $request): bool
    {
        return $this->authorize === null || ($this->authorize)($request) === true;
    }

    public function isActive(Request $request): bool
    {
        if ($this->route !== null && $request->route() !== null) {
            $current = $request->route()->getName();

            if ($current !== null && ($current === $this->route || Str::is($this->route.'.*', $current))) {
                return true;
            }
        }

        $url = $this->resolveUrl();

        if ($url === null) {
            return false;
        }

        return rtrim($request->url(), '/') === rtrim($url, '/');
    }
}
