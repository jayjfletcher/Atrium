<x-atrium::layout title="Orders">
    <x-slot:header>
        <x-atrium::page-header title="Orders" description="Every order placed in the last few days.">
            <x-slot:actions>
                <x-atrium::icon-button icon="arrow-path" label="Refresh" variant="outline" :href="route('atrium.demo.orders')" />
                <x-atrium::icon-button icon="arrow-down-tray" label="Export as CSV" variant="outline" />
            </x-slot:actions>
        </x-atrium::page-header>
    </x-slot:header>

    <x-atrium::card>
        <x-atrium::table striped>
            <x-slot:head>
                <x-atrium::table.row>
                    <x-atrium::table.cell heading>Order</x-atrium::table.cell>
                    <x-atrium::table.cell heading>Customer</x-atrium::table.cell>
                    <x-atrium::table.cell heading>Status</x-atrium::table.cell>
                    <x-atrium::table.cell heading>Placed</x-atrium::table.cell>
                    <x-atrium::table.cell heading numeric>Total</x-atrium::table.cell>
                    <x-atrium::table.cell heading><span class="sr-only">Actions</span></x-atrium::table.cell>
                </x-atrium::table.row>
            </x-slot:head>

            @foreach ($orders as $order)
                @php($status = \Workbench\App\Atrium\DemoData::orderStatus($order['status']))

                <x-atrium::table.row id="{{ $order['number'] }}" data-testid="order-{{ $order['number'] }}">
                    <x-atrium::table.cell class="font-medium">{{ $order['number'] }}</x-atrium::table.cell>
                    <x-atrium::table.cell>
                        <div class="flex flex-col">
                            <span>{{ $order['customer'] }}</span>
                            <span class="text-xs text-on-surface dark:text-on-surface-dark">{{ $order['email'] }}</span>
                        </div>
                    </x-atrium::table.cell>
                    <x-atrium::table.cell>
                        <span class="inline-flex items-center gap-2">
                            <x-atrium::status-dot :variant="$status['variant']" :label="$status['label']" />
                            {{ $status['label'] }}
                        </span>
                    </x-atrium::table.cell>
                    <x-atrium::table.cell>{{ $order['placed'] }}</x-atrium::table.cell>
                    <x-atrium::table.cell numeric>${{ number_format($order['total'], 2) }}</x-atrium::table.cell>
                    <x-atrium::table.cell numeric>
                        <span class="inline-flex items-center gap-1">
                            <x-atrium::icon-button icon="envelope" label="Email {{ $order['customer'] }}" size="sm" href="mailto:{{ $order['email'] }}" />
                            <x-atrium::icon-button icon="document-text" label="Download invoice" size="sm" />
                        </span>
                    </x-atrium::table.cell>
                </x-atrium::table.row>
            @endforeach
        </x-atrium::table>
    </x-atrium::card>
</x-atrium::layout>
