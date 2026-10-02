<?php

declare(strict_types=1);

namespace JayI\Atrium\Support;

use InvalidArgumentException;

/**
 * Inline SVG icons for navigation items, icon buttons and anywhere else.
 *
 * Every Heroicons outline icon (MIT, resources/icons/heroicons) is available
 * by its Heroicons name, such as `users` or `arrow-down-tray`. Packages add
 * their own with `register()`.
 */
final class Icons
{
    /** @var array<string, string> */
    private static array $registered = [];

    /** @var array<string, string> */
    private static array $loaded = [];

    /**
     * Add an icon, or replace one, under the given name.
     */
    public static function register(string $name, string $svg): void
    {
        self::$registered[$name] = $svg;
        unset(self::$loaded[$name]);
    }

    public static function has(string $name): bool
    {
        return isset(self::$registered[$name]) || is_file(self::file($name));
    }

    /**
     * The icon as inline SVG, sized by its container and hidden from
     * assistive technology - label what it sits in instead.
     */
    public static function svg(string $name): string
    {
        if (isset(self::$loaded[$name])) {
            return self::$loaded[$name];
        }

        $svg = self::$registered[$name] ?? null;

        if ($svg === null) {
            $file = self::file($name);

            if (! preg_match('/^[a-z0-9-]+$/', $name) || ! is_file($file)) {
                throw new InvalidArgumentException("Unknown icon [{$name}].");
            }

            $svg = trim((string) file_get_contents($file));
        }

        return self::$loaded[$name] = $svg;
    }

    private static function file(string $name): string
    {
        return dirname(__DIR__, 2).'/resources/icons/heroicons/outline/'.$name.'.svg';
    }
}
