<?php

declare(strict_types=1);

namespace Atrium\Atrium\Settings;

use Atrium\Atrium\Plugins\PluginRegistry;
use Illuminate\Http\Request;

class SettingsRegistry
{
    /** @var array<int, SettingsPanel> */
    protected array $extra = [];

    public function __construct(protected PluginRegistry $plugins) {}

    public function add(SettingsPanel $panel): static
    {
        $this->extra[] = $panel;

        return $this;
    }

    /**
     * Panels visible to the given request, sorted.
     *
     * @return array<int, SettingsPanel>
     */
    public function panels(Request $request): array
    {
        $panels = $this->extra;

        foreach ($this->plugins->authorized($request) as $plugin) {
            $panel = $plugin->settings();

            if ($panel instanceof SettingsPanel) {
                $panels[] = $panel;
            }
        }

        $panels = array_values(array_filter(
            $panels,
            fn (SettingsPanel $panel): bool => $panel->isAuthorized($request),
        ));

        usort($panels, fn (SettingsPanel $a, SettingsPanel $b): int => $a->sort <=> $b->sort
            ?: strcmp($a->label, $b->label));

        return $panels;
    }

    public function find(Request $request, string $key): ?SettingsPanel
    {
        return array_find(
            $this->panels($request),
            fn (SettingsPanel $panel): bool => $panel->key === $key,
        );
    }
}
