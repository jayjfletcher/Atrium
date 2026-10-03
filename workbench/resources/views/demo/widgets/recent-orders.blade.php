<x-atrium::card title="Recent orders" subtitle="The latest orders across every channel." class="h-full">
    <x-slot:actions>
        <x-atrium::icon-button icon="arrow-top-right-on-square" label="View all orders" size="sm"
                               :href="route('atrium.demo.orders')" />
    </x-slot:actions>

    <x-atrium::table>
        <x-slot:head>
            <x-atrium::table.row>
                <x-atrium::table.cell heading>Order</x-atrium::table.cell>
                <x-atrium::table.cell heading>Customer</x-atrium::table.cell>
                <x-atrium::table.cell heading>Status</x-atrium::table.cell>
                <x-atrium::table.cell heading numeric>Total</x-atrium::table.cell>
            </x-atrium::table.row>
        </x-slot:head>

        @foreach ($orders as $order)
            @php($status = \Workbench\App\Atrium\DemoData::orderStatus($order['status']))

            <x-atrium::table.row>
                <x-atrium::table.cell class="font-medium">{{ $order['number'] }}</x-atrium::table.cell>
                <x-atrium::table.cell>{{ $order['customer'] }}</x-atrium::table.cell>
                <x-atrium::table.cell>
                    <span class="inline-flex items-center gap-2">
                        <x-atrium::status-dot :variant="$status['variant']" :label="$status['label']" />
                        {{ $status['label'] }}
                    </span>
                </x-atrium::table.cell>
                <x-atrium::table.cell numeric>${{ number_format($order['total'], 2) }}</x-atrium::table.cell>
            </x-atrium::table.row>
        @endforeach
    </x-atrium::table>
</x-atrium::card>
