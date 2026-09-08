<?php

declare(strict_types=1);

namespace Atrium\Atrium\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakePluginCommand extends GeneratorCommand
{
    protected $name = 'atrium:plugin';

    protected $description = 'Create a new Atrium plugin class.';

    protected $type = 'Plugin';

    protected function getStub(): string
    {
        return __DIR__.'/../../../stubs/plugin.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Atrium';
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the plugin class'],
        ];
    }
}
