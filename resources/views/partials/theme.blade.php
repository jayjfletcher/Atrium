@php($atriumTheme = collect(config('atrium.theme', []))->filter(fn ($value) => is_scalar($value)))

{{-- Runs before the stylesheet paints, so a stored dark preference or a
     collapsed sidebar never flashes the wrong state on load. atrium.js owns
     changing either afterwards. --}}
<script>
    (function () {
        var root = document.documentElement
        var theme = null
        var sidebar = null

        try {
            theme = localStorage.getItem('atrium.theme')
            sidebar = localStorage.getItem('atrium.sidebar')
        } catch (error) {}

        var dark = theme === 'dark' || (theme !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches)

        root.classList.toggle('dark', dark)

        if (sidebar === 'collapsed') {
            root.dataset.atriumSidebar = 'collapsed'
        }
    })()
</script>

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
