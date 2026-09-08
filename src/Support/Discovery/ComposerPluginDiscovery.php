<?php

declare(strict_types=1);

namespace Atrium\Atrium\Support\Discovery;

use Illuminate\Filesystem\Filesystem;

/**
 * Discovers plugin classes declared by installed Composer packages under
 * their `extra.atrium.plugins` key.
 */
class ComposerPluginDiscovery
{
    public function __construct(
        protected Filesystem $files,
        protected string $vendorPath,
    ) {}

    /**
     * @return array<int, class-string>
     */
    public function discover(): array
    {
        $manifest = $this->vendorPath.'/composer/installed.json';

        if (! $this->files->exists($manifest)) {
            return [];
        }

        $contents = json_decode($this->files->get($manifest), true);

        if (! is_array($contents)) {
            return [];
        }

        $packages = $contents['packages'] ?? $contents;

        if (! is_array($packages)) {
            return [];
        }

        $plugins = [];

        foreach ($packages as $package) {
            if (! is_array($package)) {
                continue;
            }

            foreach ($this->pluginsFor($package) as $plugin) {
                $plugins[] = $plugin;
            }
        }

        return array_values(array_unique($plugins));
    }

    /**
     * @param  array<string, mixed>  $package
     * @return array<int, class-string>
     */
    protected function pluginsFor(array $package): array
    {
        $extra = $package['extra'] ?? null;

        if (! is_array($extra)) {
            return [];
        }

        $atrium = $extra['atrium'] ?? null;

        if (! is_array($atrium)) {
            return [];
        }

        $declared = $atrium['plugins'] ?? null;

        if (is_string($declared)) {
            $declared = [$declared];
        }

        if (! is_array($declared)) {
            return [];
        }

        /** @var array<int, class-string> */
        return array_values(array_filter(
            $declared,
            fn (mixed $class): bool => is_string($class) && $class !== '',
        ));
    }
}
