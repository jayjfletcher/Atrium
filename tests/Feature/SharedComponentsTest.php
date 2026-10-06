<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

it('renders a description list of terms and values', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-atrium::description-list>
            <x-atrium::description-list.item term="Status">Active</x-atrium::description-list.item>
        </x-atrium::description-list>
        BLADE);

    expect($html)->toContain('<dl')
        ->toMatch('/<dt[^>]*>Status<\/dt>/')
        ->toMatch('/<dd[^>]*>Active<\/dd>/');
});

it('renders a bare input with no label or wrapper', function (): void {
    $html = Blade::render('<x-atrium::form.input name="qty" label="Quantity" hint="How many" bare />');

    expect($html)->toContain('name="qty"')
        ->toContain('class="contents"')
        ->not->toContain('Quantity')
        ->not->toContain('How many');
});

it('renders an input with prefix and suffix addons', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-atrium::form.input name="weight" label="Weight">
            <x-slot:prefix>≈</x-slot:prefix>
            <x-slot:suffix>kg</x-slot:suffix>
        </x-atrium::form.input>
        BLADE);

    expect($html)->toContain('>kg</span>')
        ->toContain('>≈</span>')
        ->toContain('rounded-l-none')
        ->toContain('rounded-r-none');
});

it('renders a combobox seeded with its options and value', function (): void {
    $html = Blade::render('<x-atrium::form.combobox name="feature" label="Feature" :options="[\'beta\', \'gamma\']" value="beta" />');

    expect($html)->toContain('atriumCombobox(')
        ->toContain('role="combobox"')
        ->toContain('value="beta"')
        ->toContain('gamma');
});

it('renders a chip that reflects its pressed state', function (): void {
    expect(Blade::render('<x-atrium::chip active>All</x-atrium::chip>'))->toContain('aria-pressed="true"')
        ->and(Blade::render('<x-atrium::chip href="/x">Tag</x-atrium::chip>'))->toContain('<a href="/x" aria-pressed="false"');
});

it('renders a search input', function (): void {
    expect(Blade::render('<x-atrium::search-input name="q" value="cog" placeholder="Search tools" />'))
        ->toContain('type="search"')
        ->toContain('value="cog"')
        ->toContain('aria-label="Search tools"');
});

it('renders a banner with utility classes or standalone inline styles', function (): void {
    $dashboard = Blade::render('<x-atrium::banner>Careful</x-atrium::banner>');
    $standalone = Blade::render(<<<'BLADE'
        <x-atrium::banner standalone>
            Careful
            <x-slot:actions><x-atrium::banner.button standalone>Leave</x-atrium::banner.button></x-slot:actions>
        </x-atrium::banner>
        BLADE);

    expect($dashboard)->toContain('bg-warning')->not->toContain('style=')
        ->and($standalone)->toContain('style="display:flex')->toContain('Leave');
});

it('lines up form actions with labelled inputs', function (): void {
    expect(Blade::render('<x-atrium::form.actions>Go</x-atrium::form.actions>'))->toContain('sm:pt-6.5');
});

it('flashes the status and the first error', function (): void {
    session()->flash('status', 'Saved.');
    $errors = (new ViewErrorBag)->put('default', new MessageBag(['name' => 'The name is required.', 'other' => 'Other.']));

    view()->share('errors', $errors);

    $html = Blade::render('<x-atrium::flash />');

    expect($html)->toContain('Saved.')->toContain('The name is required.')->not->toContain('Other.');
});

it('flashes only the errors of the keys given', function (): void {
    $errors = (new ViewErrorBag)->put('default', new MessageBag(['name' => 'The name is required.', 'prompt' => 'Prompt failed.']));

    view()->share('errors', $errors);

    expect(Blade::render('<x-atrium::flash :keys="[\'prompt\']" />'))
        ->toContain('Prompt failed.')
        ->not->toContain('The name is required.');
});

it('renders a standalone guest page with the stylesheet', function (): void {
    $html = Blade::render('<x-atrium::guest title="Join Acme" heading="Join Acme">Welcome</x-atrium::guest>');

    expect($html)->toContain('<title>Join Acme</title>')
        ->toContain('vendor/atrium/atrium.css')
        ->toContain('data-testid="guest-layout"')
        ->toContain('Welcome');
});
