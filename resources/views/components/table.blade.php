@props([
    'striped' => false,
    'compact' => false,
])

<div class="w-full overflow-x-auto rounded-radius border border-outline dark:border-outline-dark">
    <table {{ $attributes->class('w-full min-w-max text-left text-sm text-on-surface dark:text-on-surface-dark')->merge(['data-striped' => $striped ? 'true' : 'false', 'data-compact' => $compact ? 'true' : 'false']) }}>
        @isset($head)
            <thead class="border-b border-outline bg-surface-alt text-sm text-on-surface-strong dark:border-outline-dark dark:bg-surface-dark-alt dark:text-on-surface-dark-strong">
                {{ $head }}
            </thead>
        @endisset

        <tbody class="divide-y divide-outline dark:divide-outline-dark">{{ $slot }}</tbody>

        @isset($foot)
            <tfoot class="border-t border-outline bg-surface-alt dark:border-outline-dark dark:bg-surface-dark-alt">{{ $foot }}</tfoot>
        @endisset
    </table>
</div>
