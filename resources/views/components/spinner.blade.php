@props(['size' => 'md'])

@php($sizes = ['sm' => 'size-4', 'md' => 'size-6', 'lg' => 'size-8'])

<svg {{ $attributes->class('animate-spin text-primary dark:text-primary-dark '.($sizes[$size] ?? $sizes['md'])) }}
     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" role="status" aria-label="{{ __('atrium::atrium.loading') }}">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
</svg>
