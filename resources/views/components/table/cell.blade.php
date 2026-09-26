@props([
    'heading' => false,
    'numeric' => false,
])

@php
    $classes = ($heading ? 'px-4 py-2.5 ' : 'px-4 py-3 ').($numeric ? 'text-right tabular-nums' : 'text-left');
@endphp

@if ($heading)
    <th scope="col" {{ $attributes->class('font-medium '.$classes) }}>{{ $slot }}</th>
@else
    <td {{ $attributes->class($classes.' text-on-surface-strong/90 dark:text-on-surface-dark-strong/90') }}>{{ $slot }}</td>
@endif
