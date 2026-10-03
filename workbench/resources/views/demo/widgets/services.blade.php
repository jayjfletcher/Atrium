<x-atrium::card title="Service status" subtitle="Uptime over the last 30 days." class="h-full">
    <ul class="flex flex-col gap-3">
        @foreach ($services as $service)
            <li class="flex items-center justify-between gap-3 text-sm">
                <span class="flex items-center gap-2">
                    <x-atrium::status-dot :variant="$service['status']" :label="$service['label']" />
                    <span class="font-medium text-on-surface-strong dark:text-on-surface-dark-strong">{{ $service['name'] }}</span>
                </span>
                <span class="tabular-nums">{{ $service['uptime'] }}</span>
            </li>
        @endforeach
    </ul>
</x-atrium::card>
