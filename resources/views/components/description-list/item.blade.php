@props(['term'])

<dt class="text-on-surface/80 dark:text-on-surface-dark/80">{{ $term }}</dt>
<dd {{ $attributes->class('col-span-2 min-w-0 break-words text-on-surface-strong dark:text-on-surface-dark-strong') }}>{{ $slot }}</dd>
