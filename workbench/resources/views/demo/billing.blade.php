<x-atrium::layout title="Billing">
    <x-slot:header>
        <x-atrium::page-header title="Billing" description="Shown only while the billing feature is on and you may manage billing." />
    </x-slot:header>

    <div class="flex flex-col gap-4">
        <x-atrium::card title="Growth plan" subtitle="$499 a month, renews November 1, 2026.">
            <x-atrium::progress label="Orders included this month" :value="1284" :max="2000" />
        </x-atrium::card>

        <x-atrium::card title="Invoices">
            <x-atrium::table>
                <x-slot:head>
                    <x-atrium::table.row>
                        <x-atrium::table.cell heading>Invoice</x-atrium::table.cell>
                        <x-atrium::table.cell heading>Period</x-atrium::table.cell>
                        <x-atrium::table.cell heading>Status</x-atrium::table.cell>
                        <x-atrium::table.cell heading numeric>Amount</x-atrium::table.cell>
                        <x-atrium::table.cell heading><span class="sr-only">Actions</span></x-atrium::table.cell>
                    </x-atrium::table.row>
                </x-slot:head>

                @foreach ($invoices as $invoice)
                    <x-atrium::table.row>
                        <x-atrium::table.cell class="font-medium">{{ $invoice['number'] }}</x-atrium::table.cell>
                        <x-atrium::table.cell>{{ $invoice['period'] }}</x-atrium::table.cell>
                        <x-atrium::table.cell>
                            @php($label = $invoice['status'] === 'success' ? 'Paid' : 'Due November 1')
                            <span class="inline-flex items-center gap-2">
                                <x-atrium::status-dot :variant="$invoice['status']" :label="$label" /> {{ $label }}
                            </span>
                        </x-atrium::table.cell>
                        <x-atrium::table.cell numeric>{{ $invoice['amount'] }}</x-atrium::table.cell>
                        <x-atrium::table.cell numeric>
                            <x-atrium::icon-button icon="arrow-down-tray" label="Download {{ $invoice['number'] }}" size="sm" />
                        </x-atrium::table.cell>
                    </x-atrium::table.row>
                @endforeach
            </x-atrium::table>
        </x-atrium::card>
    </div>
</x-atrium::layout>
