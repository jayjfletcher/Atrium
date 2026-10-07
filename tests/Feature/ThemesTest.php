<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use JayI\Atrium\Domains\Access\Services\Gatekeeper;
use JayI\Atrium\Domains\Themes\Data\Theme;
use JayI\Atrium\Domains\Themes\Services\ThemeRegistry;
use JayI\Atrium\Facades\Atrium;

beforeEach(function (): void {
    app()->detectEnvironment(fn (): string => 'local');
});

it('ships the atrium and harbor themes, atrium first', function (): void {
    $themes = app(ThemeRegistry::class);

    expect(array_keys($themes->all()))->toBe(['atrium', 'harbor'])
        ->and($themes->default()?->key)->toBe('atrium')
        ->and($themes->find('harbor')?->properties())->toMatchArray([
            '--color-primary' => '#0d9488',
            '--color-primary-dark' => '#2dd4bf',
            '--radius-radius' => '0.75rem',
        ]);
});

it('adds themes from config and from packages', function (): void {
    config()->set('atrium.themes.available', [
        'sunset' => ['label' => 'Sunset', 'swatch' => '#ea580c', 'colors' => ['primary' => '#ea580c']],
    ]);
    app()->forgetInstance(ThemeRegistry::class);

    Atrium::theme(Theme::make('forest')->label('Forest')->colors(['primary' => '#15803d']));

    $themes = app(ThemeRegistry::class)->all();

    expect(array_keys($themes))->toBe(['atrium', 'harbor', 'sunset', 'forest'])
        ->and($themes['sunset']->label)->toBe('Sunset')
        ->and($themes['sunset']->swatchColor())->toBe('#ea580c')
        ->and($themes['forest']->properties())->toBe(['--color-primary' => '#15803d']);
});

it('falls back to the first theme when the default names none', function (): void {
    config()->set('atrium.themes.default', 'missing');

    expect(app(ThemeRegistry::class)->default()?->key)->toBe('atrium');
});

it('keeps values that could break out of the style block away from it', function (): void {
    $theme = Theme::make('evil')->colors(['primary' => 'red; } body { display: none', 'outline' => '#eee'])->radius('</style>')->swatch('url("x")');

    expect($theme->properties())->toBe(['--color-outline' => '#eee'])
        ->and($theme->swatchColor())->toBe('var(--color-primary)');
});

it('applies the long-standing theme overrides to the atrium theme', function (): void {
    config()->set('atrium.theme', ['primary' => '#ff0000']);
    app()->forgetInstance(ThemeRegistry::class);

    expect(app(ThemeRegistry::class)->find('atrium')?->properties())->toBe(['--color-primary' => '#ff0000']);
});

it('shows the switcher beside the light and dark toggle', function (): void {
    $html = $this->get('/atrium')->assertOk()->getContent();

    expect($html)->toContain('data-testid="theme-switcher"')
        ->toContain('data-testid="palette-atrium"')
        ->toContain('data-testid="palette-harbor"')
        ->toContain(':root[data-atrium-theme="harbor"]')
        ->toContain('--color-primary: #0d9488')
        ->and(strpos($html, 'theme-switcher'))->toBeLessThan(strpos($html, 'appearance-toggle'));
});

it('shows the switcher only while its feature is on', function (): void {
    config()->set('atrium.themes.switcher_feature', 'theme-switcher');
    app(Gatekeeper::class)->resolveFeaturesUsing(fn (string $feature): bool => $feature !== 'theme-switcher');

    $html = $this->get('/atrium')->assertOk()->getContent();

    // Only the default theme is applied, whatever a browser remembers.
    expect($html)->not->toContain('data-testid="theme-switcher"')
        ->not->toContain('harbor');
});

it('shows the switcher when the pennantplus feature is not installed', function (): void {
    config()->set('atrium.themes.switcher_feature', 'Missing\\ThemeSwitcherFeature');
    app(Gatekeeper::class)->resolveFeaturesUsing(fn (): bool => false);

    expect(app(ThemeRegistry::class)->switchable(request()))->toBeTrue();
});

it('hides the switcher with a single theme', function (): void {
    $themes = new ThemeRegistry(config(), app(Gatekeeper::class));
    $themes->register(Theme::make('only'));

    expect($themes->switchable(request()))->toBeFalse();
});

it('applies themes on standalone guest pages too', function (): void {
    expect(Blade::render('<x-atrium::guest>Hi</x-atrium::guest>'))->toContain('data-atrium-themes');
});
