<?php

declare(strict_types=1);

namespace Atrium\Atrium\Tests\Fixtures;

use Atrium\Atrium\Plugins\Plugin;
use Atrium\Atrium\Search\SearchResult;
use Atrium\Atrium\Search\SearchSource;
use Atrium\Atrium\Settings\SettingsPanel;

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
