<x-atrium::card title="Quarterly goals" subtitle="Q4 2026: October to December." class="h-full">
    <div class="flex flex-col gap-4">
        @foreach ($goals as $goal)
            <x-atrium::progress :label="$goal['label']" :value="$goal['value']" :max="$goal['max']" />
        @endforeach
    </div>
</x-atrium::card>
