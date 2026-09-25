<?php

declare(strict_types=1);

namespace JayI\Atrium\Plugins;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use JayI\Atrium\Contracts\Plugin as PluginContract;
use JayI\Atrium\Search\SearchSource;
use JayI\Atrium\Settings\SettingsPanel;

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
