@props([
    'heading' => false,
    'numeric' => false,
])

@php
    $classes = 'px-4 py-3 '.($numeric ? 'text-right tabular-nums' : 'text-left');
@endphp

@if ($heading)
    <th scope="col" {{ $attributes->class('font-medium '.$classes) }}>{{ $slot }}</th>
@else
    <td {{ $attributes->class($classes) }}>{{ $slot }}</td>
@endif
