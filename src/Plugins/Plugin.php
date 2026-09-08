<?php

declare(strict_types=1);

namespace Atrium\Atrium\Plugins;

use Atrium\Atrium\Contracts\Plugin as PluginContract;
use Atrium\Atrium\Search\SearchSource;
use Atrium\Atrium\Settings\SettingsPanel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Convenience base class providing no-op defaults for every optional
 * plugin method. A plugin contributing only navigation implements one method.
 */
abstract class Plugin implements PluginContract
{
    public function key(): string
    {
        return Str::of(class_basename($this))
            ->beforeLast('Plugin')
            ->kebab()
            ->toString();
    }

    public function label(): string
    {
        return Str::of($this->key())->replace('-', ' ')->headline()->toString();
    }

    public function authorize(Request $request): bool
    {
        return true;
    }

    public function navigation(): array
    {
        return [];
    }

    public function routes(): void
    {
        //
    }

    public function settings(): ?SettingsPanel
    {
        return null;
    }

    public function widgets(): array
    {
        return [];
    }

    public function search(): ?SearchSource
    {
        return null;
    }
}
