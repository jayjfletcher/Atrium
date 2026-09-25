<?php

declare(strict_types=1);

namespace JayI\Atrium\Tests\Fixtures;

use JayI\Atrium\Plugins\Plugin;
use JayI\Atrium\Search\SearchResult;
use JayI\Atrium\Search\SearchSource;
use JayI\Atrium\Settings\SettingsPanel;

class FullPlugin extends Plugin
{
    public function key(): string
    {
        return 'full';
    }

    public function settings(): ?SettingsPanel
    {
        return SettingsPanel::make('full')
            ->label('Full Settings')
            ->description('Everything the full plugin can configure.')
            ->view('full-settings');
    }

    public function search(): ?SearchSource
    {
        return SearchSource::make('full')
            ->label('Full')
            ->using(fn (string $query): array => [
                SearchResult::make('Match for '.$query, '/atrium/full')->subtitle('From the full plugin'),
            ]);
    }
}
