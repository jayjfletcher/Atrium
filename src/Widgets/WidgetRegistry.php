<?php

declare(strict_types=1);

namespace Atrium\Atrium\Widgets;

use Atrium\Atrium\Exceptions\DuplicateWidgetException;
use Atrium\Atrium\Plugins\PluginRegistry;
use Illuminate\Http\Request;

/**
 * Holds the widget types plugins have made available.
 *
 * Registration offers a widget in the picker. It never places one on a
 * dashboard; placement is always an explicit user action.
 */
class WidgetRegistry
{
    /** @var array<string, WidgetDefinition>|null */
    protected ?array $definitions = null;

    /** @var array<string, string> */
    protected array $owners = [];

    /** @var array<int, WidgetDefinition> */
    protected array $extra = [];

    public function __construct(protected PluginRegistry $plugins) {}

    /**
     * Register a widget outside of any plugin, e.g. from the host app.
     */
    public function add(WidgetDefinition $definition): static
    {
        $this->extra[] = $definition;
        $this->definitions = null;

        return $this;
    }

    /**
     * Every available widget type.
     *
     * @return array<string, WidgetDefinition>
     */
    public function all(): array
    {
        if ($this->definitions !== null) {
            return $this->definitions;
        }

        $definitions = [];
        $this->owners = [];

        foreach ($this->extra as $definition) {
            $definitions[$definition->key] = $definition;
            $this->owners[$definition->key] = 'app';
        }

        foreach ($this->plugins->all() as $key => $plugin) {
            foreach ($plugin->widgets() as $definition) {
                if (isset($definitions[$definition->key])) {
                    throw DuplicateWidgetException::forKey(
                        $definition->key,
                        $this->owners[$definition->key],
                        $key,
                    );
                }

                $definitions[$definition->key] = $definition;
                $this->owners[$definition->key] = $key;
            }
        }

        return $this->definitions = $definitions;
    }

    public function has(string $key): bool
    {
        return isset($this->all()[$key]);
    }

    public function get(string $key): ?WidgetDefinition
    {
        return $this->all()[$key] ?? null;
    }

    /**
     * Widget types the given request may place on a dashboard.
     *
     * @return array<string, WidgetDefinition>
     */
    public function available(Request $request): array
    {
        $authorized = $this->plugins->authorized($request);

        return array_filter(
            $this->all(),
            function (WidgetDefinition $definition, string $key) use ($authorized, $request): bool {
                $owner = $this->owners[$key] ?? 'app';

                if ($owner !== 'app' && ! isset($authorized[$owner])) {
                    return false;
                }

                return $definition->isAuthorized($request);
            },
            ARRAY_FILTER_USE_BOTH,
        );
    }
}
