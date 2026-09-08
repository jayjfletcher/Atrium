<?php

declare(strict_types=1);

use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Blade;

function renderPagination(mixed $paginator): string
{
    return Blade::render('<x-atrium::pagination :paginator="$paginator" />', ['paginator' => $paginator]);
}

it('renders nothing without a paginator', function (): void {
    expect(trim(renderPagination(null)))->toBe('');
});

it('renders nothing when everything fits on one page', function (): void {
    $paginator = new LengthAwarePaginator([1, 2], 2, 10, 1);

    expect(trim(renderPagination($paginator)))->toBe('');
});

it('shows the total for a length aware paginator', function (): void {
    $paginator = new LengthAwarePaginator([1, 2], 40, 2, 2, ['path' => '/things']);

    $html = renderPagination($paginator);

    expect($html)->toContain('Showing 3 to 4 of 40')
        ->toContain('Previous')
        ->toContain('Next');
});

it('links both directions from a middle page', function (): void {
    $paginator = new LengthAwarePaginator([1, 2], 40, 2, 2, ['path' => '/things']);

    $html = renderPagination($paginator);

    expect(substr_count($html, '<a href'))->toBe(2);
});

it('disables previous on the first page', function (): void {
    $paginator = new LengthAwarePaginator([1, 2], 40, 2, 1, ['path' => '/things']);

    $html = renderPagination($paginator);

    expect(substr_count($html, '<a href'))->toBe(1)
        ->and($html)->toContain('cursor-not-allowed');
});

it('renders a cursor paginator without asking for a total', function (): void {
    // A cursor paginator knows there is a next page by being handed one more
    // row than the page size. It has no total(), firstItem() or onFirstPage(),
    // so this would fatal if the component assumed a length-aware one.
    $paginator = new CursorPaginator([1, 2, 3], 2, null, ['path' => '/runs']);

    $html = renderPagination($paginator);

    expect($html)->toContain('Showing 2')
        ->not->toContain('of');
});

it('disables previous on a cursor paginator with no cursor yet', function (): void {
    $paginator = new CursorPaginator([1, 2, 3], 2, null, ['path' => '/runs']);

    $html = renderPagination($paginator);

    expect($html)->toContain('Previous')
        ->and(substr_count($html, 'cursor-not-allowed'))->toBeGreaterThan(0);
});
