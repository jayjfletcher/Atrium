<?php

declare(strict_types=1);

namespace JayI\Atrium\Testing;

use Symfony\Component\Finder\Finder;

/**
 * Checks a package's views against Atrium's compiled stylesheet.
 *
 * Atrium ships one precompiled stylesheet, and first-party packages ship
 * none, so a Tailwind class Atrium did not compile silently does nothing.
 * Each package's test suite asserts both lists are empty:
 *
 *     expect(AtriumStyles::missingClasses(__DIR__.'/../../resources/views'))->toBe([]);
 *     expect(AtriumStyles::inlineStyles(__DIR__.'/../../resources/views'))->toBe([]);
 */
final class AtriumStyles
{
    /**
     * Classes used in the views that Atrium's stylesheet does not contain,
     * each with the first view that uses it.
     *
     * @return array<string, string>
     */
    public static function missingClasses(string $views, ?string $stylesheet = null): array
    {
        $css = (string) file_get_contents($stylesheet ?? dirname(__DIR__, 2).'/public/atrium.css');
        $missing = [];

        foreach (self::views($views) as $path => $contents) {
            foreach (self::classes($contents) as $class) {
                $selector = '.'.preg_replace('/([:\/.\[\]%!@&*()#,>+~=])/', '\\\\$1', $class);

                if (! str_contains($css, $selector)) {
                    $missing[$class] ??= $path;
                }
            }
        }

        ksort($missing);

        return $missing;
    }

    /**
     * Views that style themselves with a `<style>` block or a `style`
     * attribute instead of Atrium's components and utilities.
     *
     * @return list<string>
     */
    public static function inlineStyles(string $views): array
    {
        $offending = [];

        foreach (self::views($views) as $path => $contents) {
            if (preg_match('/<style\b|(?<![\w:-])style\s*=\s*["\']/i', $contents) === 1) {
                $offending[] = $path;
            }
        }

        sort($offending);

        return $offending;
    }

    /**
     * @return array<string, string>
     */
    private static function views(string $directory): array
    {
        if (! is_dir($directory)) {
            return [];
        }

        $views = [];

        foreach ((new Finder)->files()->in($directory)->name('*.blade.php') as $file) {
            $views[$file->getRelativePathname()] = $file->getContents();
        }

        ksort($views);

        return $views;
    }

    /**
     * Plain class lists, `wrapper` props, and the quoted class strings in
     * `@class([...])` and Alpine's bound `:class`.
     *
     * @return list<string>
     */
    private static function classes(string $contents): array
    {
        preg_match_all('/(?<![:\w-])(?:class|wrapper)="([^"]*)"/', $contents, $plain);
        $values = $plain[1];

        preg_match_all('/@class\(\[(.*?)\]\)|(?:x-bind:|:)class="([^"]*)"/s', $contents, $expressions);

        foreach ([...$expressions[1], ...$expressions[2]] as $expression) {
            // In `'classes' => $condition`, only the key names classes.
            $expression = (string) preg_replace('/=>[^\n]*/', '', $expression);

            preg_match_all("/'([^']*)'/", $expression, $strings);
            $values = [...$values, ...$strings[1]];
        }

        $classes = [];

        foreach ($values as $value) {
            $value = (string) preg_replace('/\{\{.*?\}\}|\{!!.*?!!\}|@\w+(\(.*?\))?/s', ' ', $value);

            foreach (preg_split('/\s+/', $value, flags: PREG_SPLIT_NO_EMPTY) ?: [] as $class) {
                if (preg_match('/^[a-z!-][a-z0-9:\-\/.\[\]%!&_*()#>+~=,]*$/', $class) === 1 && ! str_contains($class, '$')) {
                    $classes[] = $class;
                }
            }
        }

        return array_values(array_unique($classes));
    }
}
