{{-- An icon by name: any Heroicons outline icon, or one a package registered. --}}
@props(['name'])

<span {{ $attributes->class('inline-flex shrink-0 [&_svg]:size-full') }} aria-hidden="true">{!! \RefactorCircus\Atrium\Support\Icons::svg($name) !!}</span>
