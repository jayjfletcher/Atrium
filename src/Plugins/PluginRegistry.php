<?php

declare(strict_types=1);

namespace Atrium\Atrium\Plugins;

use Atrium\Atrium\Contracts\Plugin as PluginContract;
use Atrium\Atrium\Exceptions\InvalidPluginException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;

class PluginRegistry
{
    /** @var array<string, PluginContract> */
    protected array $plugins = [];

    /** @var array<int, string> */
    protected array $disabled = [];

    public function __construct(protected Container $container) {}

    /**
     * Keys the host app has disabled in config.
     *
     * @param  array<int, string>  $keys
     */
    public function disable(array $keys): static
    {
        $this->disabled = $keys;

        return $this;
    }

    /**
     * @param  PluginContract|class-string  $plugin
     */
    public function register(PluginContract|string $plugin): static
    {
        $instance = $this->resolve($plugin);

        $key = $instance->key();

        if (in_array($key, $this->disabled, true)) {
            return $this;
        }

        if (isset($this->plugins[$key]) && $instance::class !== $this->plugins[$key]::class) {
            throw InvalidPluginException::duplicateKey(
                $key,
                $this->plugins[$key]::class,
                $instance::class,
            );
        }

        $this->plugins[$key] = $instance;

        return $this;
    }

    /**
     * @param  array<int, PluginContract|class-string>  $plugins
     */
    public function registerMany(array $plugins): static
    {
        foreach ($plugins as $plugin) {
            $this->register($plugin);
        }

        return $this;
    }

    public function has(string $key): bool
    {
        return isset($this->plugins[$key]);
    }

    public function get(string $key): ?PluginContract
    {
        return $this->plugins[$key] ?? null;
    }

    /**
     * Every registered plugin, regardless of authorization.
     *
     * @return array<string, PluginContract>
     */
    public function all(): array
    {
        return $this->plugins;
    }

    /**
     * Plugins the given request is authorized to see.
     *
     * @return array<string, PluginContract>
     */
    public function authorized(Request $request): array
    {
        return array_filter(
            $this->plugins,
            fn (PluginContract $plugin): bool => $plugin->authorize($request),
        );
    }

    /**
     * @param  PluginContract|class-string  $plugin
     */
    protected function resolve(PluginContract|string $plugin): PluginContract
    {
        if ($plugin instanceof PluginContract) {
            return $plugin;
        }

        if (! class_exists($plugin)) {
            throw InvalidPluginException::missingClass($plugin);
        }

        if (! is_subclass_of($plugin, PluginContract::class)) {
            throw InvalidPluginException::notAPlugin($plugin);
        }

        /** @var PluginContract */
        return $this->container->make($plugin);
    }
}
