@php($atriumTheme = collect(config('atrium.theme', []))->filter(fn ($value) => is_scalar($value)))

@if ($atriumTheme->isNotEmpty())
    {{-- Theme config overrides the compiled defaults at runtime, so an
         application can retheme the dashboard without rebuilding any CSS. --}}
    <style>
        :root {
            @foreach ($atriumTheme as $key => $value)--color-{{ $key }}: {{ $value }};
            @endforeach
        }
    </style>
@endif
