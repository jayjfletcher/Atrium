<?php

declare(strict_types=1);

namespace Atrium\Atrium\Settings;

use Closure;
use Illuminate\Http\Request;

class SettingsPanel
{
    public private(set) string $label;

    public private(set) ?string $description = null;

    public private(set) ?string $icon = null;

    public private(set) ?string $view = null;

    public private(set) ?string $component = null;

    public private(set) int $sort = 100;

    /** @var (Closure(): array<string, mixed>)|null */
    private ?Closure $resolver = null;

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

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function view(string $view): static
    {
        $this->view = $view;
        $this->component = null;

        return $this;
    }

    public function component(string $component): static
    {
        $this->component = $component;
        $this->view = null;

        return $this;
    }

    public function sort(int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @param  Closure(): array<string, mixed>  $resolver
     */
    public function resolve(Closure $resolver): static
    {
        $this->resolver = $resolver;

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
     * @return array<string, mixed>
     */
    public function resolveData(): array
    {
        return $this->resolver === null ? [] : ($this->resolver)();
    }

    public function isAuthorized(Request $request): bool
    {
        return $this->authorize === null || ($this->authorize)($request) === true;
    }
}
