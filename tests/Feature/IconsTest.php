<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use RefactorCircus\Atrium\Support\Icons;

it('serves any heroicons outline icon by name', function (): void {
    expect(Icons::svg('users'))->toStartWith('<svg')->toContain('aria-hidden="true"')
        ->and(Icons::has('arrow-down-tray'))->toBeTrue()
        ->and(Icons::has('no-such-icon'))->toBeFalse();
});

it('refuses unknown icons and names that are not icon names', function (string $name): void {
    Icons::svg($name);
})->with(['no-such-icon', '../../composer', 'Users'])->throws(InvalidArgumentException::class);

it('lets packages register their own icons', function (): void {
    Icons::register('atrium-test-mark', '<svg data-test="mark"></svg>');

    expect(Icons::svg('atrium-test-mark'))->toBe('<svg data-test="mark"></svg>')
        ->and(Icons::has('atrium-test-mark'))->toBeTrue();
});

it('renders an icon-only button labelled by its tooltip', function (): void {
    $html = Blade::render('<x-atrium::icon-button icon="trash" label="Delete" variant="danger" type="submit" data-testid="delete" />');

    expect($html)->toContain('aria-label="Delete"')
        ->toContain('type="submit"')
        ->toContain('data-testid="delete"')
        ->toContain('role="tooltip"')
        ->toContain('>Delete<')
        ->toContain('<svg')
        ->toContain('bg-danger');
});

it('renders an icon-only link button', function (): void {
    $html = Blade::render('<x-atrium::icon-button icon="plus" label="New" href="/new" size="sm" />');

    expect($html)->toContain('href="/new"')->toContain('w-8')->not->toContain('type="button"');
});

it('renders a status as a labelled dot in its colour', function (string $variant, string $class): void {
    $html = Blade::render('<x-atrium::status-dot :variant="$variant" label="Pending" data-status="pending" />', ['variant' => $variant]);

    expect($html)->toContain('role="img"')
        ->toContain('aria-label="Pending"')
        ->toContain('data-status="pending"')
        ->toContain($class);
})->with([
    ['info', 'bg-info'],
    ['success', 'bg-success'],
    ['warning', 'bg-warning'],
    ['danger', 'bg-danger'],
    ['primary', 'bg-primary'],
    ['neutral', 'bg-current'],
]);

it('renders an icon by name', function (): void {
    expect(Blade::render('<x-atrium::icon name="users" class="size-5" />'))->toContain('size-5')->toContain('<svg');
});
