<x-atrium::layout title="Tooltip">
    {{-- A tooltip inside a box that clips anything overflowing it. --}}
    <div data-testid="clipping-box" style="overflow: hidden; width: 4rem; height: 2.5rem; margin-top: 6rem;">
        <x-atrium::tooltip text="A tooltip far wider than its box">
            <x-atrium::button data-testid="tooltip-trigger">Hi</x-atrium::button>
        </x-atrium::tooltip>
    </div>

    <p data-testid="elsewhere" style="margin-top: 4rem;">Somewhere else to point at.</p>
</x-atrium::layout>
