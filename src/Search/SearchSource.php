<?php

declare(strict_types=1);

namespace Atrium\Atrium\Search;

use Closure;
use Illuminate\Http\Request;

class SearchSource
{
    public private(set) string $label;

    /** @var (Closure(string): array<int, SearchResult>)|null */
    private ?Closure $handler = null;

    /** @var (Closure(Request): bool)|null */
    private ?Closure $authorize = null;

    final public function __construct(public readonly string $key)
    {
        $this->label = $key;
    }

    public static function make(string $key): static
    {
        return new static($key);
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param  Closure(string): array<int, SearchResult>  $handler
     */
    public function using(Closure $handler): static
    {
        $this->handler = $handler;

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
     * @return array<int, SearchResult>
     */
    public function results(string $query): array
    {
        return $this->handler === null ? [] : ($this->handler)($query);
    }

    public function isAuthorized(Request $request): bool
    {
        return $this->authorize === null || ($this->authorize)($request) === true;
    }
}
