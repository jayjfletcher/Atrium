<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

function render(string $template, array $data = []): string
{
    return trim(Blade::render($template, $data));
}

it('renders a card with a title and slot content', function (): void {
    $html = render('<x-atrium::card title="Revenue">Body text</x-atrium::card>');

    expect($html)->toContain('Revenue')
        ->toContain('Body text')
        ->toContain('rounded-radius');
});

it('renders a stat with a value and trend', function (): void {
    $html = render('<x-atrium::stat label="Orders" value="1,204" change="+12%" trend="up" />');

    expect($html)->toContain('Orders')
        ->toContain('1,204')
        ->toContain('text-success');
});

it('renders a button as a link when given an href', function (): void {
    $html = render('<x-atrium::button href="/go">Go</x-atrium::button>');

    expect($html)->toContain('<a')->toContain('href="/go"')->toContain('Go');
});

it('renders a button as a button element by default', function (): void {
    $html = render('<x-atrium::button>Save</x-atrium::button>');

    expect($html)->toContain('<button')->toContain('type="button"');
});

it('renders a badge with a variant class', function (): void {
    expect(render('<x-atrium::badge variant="success">Live</x-atrium::badge>'))
        ->toContain('text-success')
        ->toContain('Live');
});

it('renders a table with head and body cells', function (): void {
    $html = render(<<<'BLADE'
        <x-atrium::table>
            <x-slot:head>
                <x-atrium::table.row><x-atrium::table.cell heading>Name</x-atrium::table.cell></x-atrium::table.row>
            </x-slot:head>
            <x-atrium::table.row><x-atrium::table.cell>Ada</x-atrium::table.cell></x-atrium::table.row>
        </x-atrium::table>
    BLADE);

    expect($html)->toContain('<th')->toContain('Name')->toContain('<td')->toContain('Ada');
});

it('renders an empty state', function (): void {
    expect(render('<x-atrium::empty-state title="Nothing here" description="Add one." />'))
        ->toContain('Nothing here')
        ->toContain('Add one.');
});

it('renders an alert with its variant', function (): void {
    expect(render('<x-atrium::alert variant="danger" title="Failed">Try again</x-atrium::alert>'))
        ->toContain('text-danger')
        ->toContain('Failed')
        ->toContain('role="alert"');
});

it('renders a text input with a label and name', function (): void {
    $html = render('<x-atrium::form.input name="email" label="Email address" type="email" />');

    expect($html)->toContain('Email address')
        ->toContain('name="email"')
        ->toContain('type="email"');
});

it('renders a select with its options', function (): void {
    $html = render('<x-atrium::form.select name="role" label="Role" :options="$options" selected="admin" />', [
        'options' => ['admin' => 'Administrator', 'user' => 'User'],
    ]);

    expect($html)->toContain('Administrator')
        ->toContain('value="admin" selected');
});

it('renders a checkbox in a checked state', function (): void {
    expect(render('<x-atrium::form.checkbox name="active" label="Active" :checked="true" />'))
        ->toContain('type="checkbox"')
        ->toContain('checked');
});

it('renders a textarea with its current value', function (): void {
    expect(render('<x-atrium::form.textarea name="notes" label="Notes" value="Hello" />'))
        ->toContain('<textarea')
        ->toContain('Hello');
});

it('renders a page header with actions', function (): void {
    $html = render(<<<'BLADE'
        <x-atrium::page-header title="Users" description="Everyone">
            <x-slot:actions><x-atrium::button>New</x-atrium::button></x-slot:actions>
        </x-atrium::page-header>
    BLADE);

    expect($html)->toContain('Users')->toContain('Everyone')->toContain('New');
});

it('renders components outside the dashboard shell', function (): void {
    // No layout, no shell, no dashboard route. This is the whole point of
    // shipping the library as standalone Blade components.
    $html = render('<div class="host-app"><x-atrium::card title="Standalone">Works</x-atrium::card></div>');

    expect($html)->toContain('host-app')
        ->toContain('Standalone')
        ->toContain('Works')
        ->not->toContain('<aside');
});
