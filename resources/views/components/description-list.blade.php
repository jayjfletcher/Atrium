{{--
    Label/value pairs, such as a record's details:

        <x-atrium::description-list>
            <x-atrium::description-list.item :term="__('Status')">Active</x-atrium::description-list.item>
        </x-atrium::description-list>
--}}
<dl {{ $attributes->class('grid grid-cols-3 gap-x-4 gap-y-2 text-sm') }}>
    {{ $slot }}
</dl>
