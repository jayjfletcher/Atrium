@php($peak = max($report['rows']))

<x-atrium::layout :title="$report['title']">
    <x-slot:header>
        <x-atrium::page-header :title="$report['title']" :description="$report['description']">
            <x-slot:actions>
                <x-atrium::icon-button icon="arrow-down-tray" label="Download report" variant="outline" />
            </x-slot:actions>
        </x-atrium::page-header>
    </x-slot:header>

    <x-atrium::card title="Last six months" subtitle="Each bar is relative to the best month.">
        <div class="flex flex-col gap-4">
            @foreach ($report['rows'] as $month => $value)
                <div class="flex items-center gap-4" data-testid="report-row">
                    <span class="shrink-0 text-sm" style="width: 6rem">{{ $month }}</span>
                    <x-atrium::progress :value="$value" :max="$peak" />
                    <span class="shrink-0 text-right text-sm font-medium tabular-nums" style="width: 6rem">{{ $report['unit'] }}{{ number_format($value) }}</span>
                </div>
            @endforeach
        </div>
    </x-atrium::card>
</x-atrium::layout>
