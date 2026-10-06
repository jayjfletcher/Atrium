{{--
    A row of buttons at the end of a form row, lined up with the inputs beside
    it (below their labels) whatever hints or errors follow them.
--}}
<div {{ $attributes->class('flex flex-wrap items-center gap-2 sm:pt-6.5') }}>
    {{ $slot }}
</div>
