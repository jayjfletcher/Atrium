<x-atrium::dropdown>
    <x-slot:trigger>
        <x-atrium::button variant="ghost">{{ $dashboard?->name ?? __('atrium::atrium.dashboards') }}</x-atrium::button>
    </x-slot:trigger>

    <ul class="mb-2 flex flex-col gap-0.5">
        @foreach ($dashboards as $option)
            <li>
                <a href="{{ route('atrium.dashboard.show', $option->slug) }}"
                   @class([
                       'flex items-center justify-between gap-2 rounded-radius px-2.5 py-2 text-sm transition hover:bg-surface-alt dark:hover:bg-surface-dark-alt',
                       'font-semibold' => $dashboard && $option->is($dashboard),
                   ])>
                    {{ $option->name }}

                    @if ($option->is_shared)
                        <x-atrium::badge>{{ __('atrium::atrium.dashboards') }}</x-atrium::badge>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>

    <form class="flex items-end gap-2 border-t border-outline pt-2 dark:border-outline-dark"
          method="POST" action="{{ route('atrium.dashboards.store') }}">
        @csrf
        <x-atrium::form.input name="name" :placeholder="__('atrium::atrium.dashboards')" />
        <x-atrium::button type="submit" size="sm">+</x-atrium::button>
    </form>
</x-atrium::dropdown>
