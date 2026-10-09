<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Search\Data;

use Closure;
use Illuminate\Http\Request;
use Laravel\SerializableClosure\SerializableClosure;

class SearchSource
{
    public private(set) string $label;

    /** What this source finds, which helps classification pick it. */
    public private(set) ?string $description = null;

    /** Seconds this source may run, overriding `atrium.search.timeout`. */
    public private(set) ?int $timeout = null;

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

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function timeout(int $seconds): static
    {
        $this->timeout = $seconds;

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

    /**
     * Sources are serialized to run in another process. Their closures are
     * wrapped by hand, since typed properties cannot hold the wrapper.
     *
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'description' => $this->description,
            'timeout' => $this->timeout,
            'handler' => $this->handler === null ? null : new SerializableClosure($this->handler),
            'authorize' => $this->authorize === null ? null : new SerializableClosure($this->authorize),
        ];
    }

    /**
     * @param  array{key: string, label: string, description: ?string, timeout: ?int, handler: ?SerializableClosure, authorize: ?SerializableClosure}  $data
     */
    public function __unserialize(array $data): void
    {
        $this->key = $data['key'];
        $this->label = $data['label'];
        $this->description = $data['description'];
        $this->timeout = $data['timeout'];
        $this->handler = $data['handler']?->getClosure();
        $this->authorize = $data['authorize']?->getClosure();
    }
}
