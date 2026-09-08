<?php

declare(strict_types=1);

namespace Atrium\Atrium\Console\Commands;

use Atrium\Atrium\Contracts\Plugin;
use Atrium\Atrium\Plugins\PluginRegistry;
use Atrium\Atrium\Widgets\WidgetRegistry;
use Illuminate\Console\Command;

class PluginListCommand extends Command
{
    protected $signature = 'atrium:plugins';

    protected $description = 'List the Atrium plugins currently registered.';

    public function handle(PluginRegistry $plugins, WidgetRegistry $widgets): int
    {
        $registered = $plugins->all();

        if ($registered === []) {
            $this->components->warn('No Atrium plugins are registered.');

            return self::SUCCESS;
        }

        $this->table(
            ['Key', 'Label', 'Nav items', 'Widgets', 'Settings', 'Search'],
            array_map(fn (Plugin $plugin): array => [
                $plugin->key(),
                $plugin->label(),
                (string) count($plugin->navigation()),
                (string) count($plugin->widgets()),
                $plugin->settings() === null ? '-' : 'yes',
                $plugin->search() === null ? '-' : 'yes',
            ], $registered),
        );

        $this->components->info(sprintf(
            '%d plugin(s) registered, offering %d widget(s).',
            count($registered),
            count($widgets->all()),
        ));

        return self::SUCCESS;
    }
}
