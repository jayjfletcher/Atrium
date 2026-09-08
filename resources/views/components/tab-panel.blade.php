@props(['name'])

<div {{ $attributes }} x-show="tab === @js($name)" x-cloak role="tabpanel">{{ $slot }}</div>
