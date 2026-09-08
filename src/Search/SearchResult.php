<?php

declare(strict_types=1);

namespace Atrium\Atrium\Search;

class SearchResult
{
    public private(set) ?string $subtitle = null;

    public private(set) ?string $icon = null;

    public private(set) ?string $group = null;

    final public function __construct(
        public readonly string $title,
        public readonly string $url,
    ) {}

    public static function make(string $title, string $url): static
    {
        return new static($title, $url);
    }

    public function subtitle(string $subtitle): static
    {
        $this->subtitle = $subtitle;

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

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'url' => $this->url,
            'subtitle' => $this->subtitle,
            'icon' => $this->icon,
            'group' => $this->group,
        ];
    }
}
