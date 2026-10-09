<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Support;

/**
 * Styles packages add to the dashboard, emitted in its <head> after Atrium's
 * own stylesheet. Atrium ships one precompiled stylesheet built from its own
 * views, so this is how a plugin styles markup Atrium never uses.
 */
class StyleRegistry
{
    /** @var array<int, string> */
    protected array $stylesheets = [];

    /** @var array<string, string> */
    protected array $css = [];

    /**
     * Link a stylesheet, such as a package's published asset.
     */
    public function stylesheet(string $href): static
    {
        if (! in_array($href, $this->stylesheets, true)) {
            $this->stylesheets[] = $href;
        }

        return $this;
    }

    /**
     * Add CSS inline. A key keeps it from being added twice, and lets a later
     * call replace it.
     */
    public function css(string $css, ?string $key = null): static
    {
        $this->css[$key ?? $css] = $css;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function stylesheets(): array
    {
        return $this->stylesheets;
    }

    /**
     * @return array<int, string>
     */
    public function inline(): array
    {
        return array_values($this->css);
    }
}
