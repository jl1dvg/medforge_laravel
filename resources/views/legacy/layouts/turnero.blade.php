@php
    use Illuminate\Support\Facades\Vite;
    use Illuminate\Support\Str;

    $title = trim($__env->yieldContent('title') ?? '');
    $pageTitle = $title !== '' ? "MedForge - {$title}" : 'MedForge';
    $bodyClass = trim($__env->yieldContent('body_class') ?? '') ?: 'turnero-body';

    $defaultStyles = [
        asset('css/vendors_css.css'),
        asset('css/style.css'),
        asset('css/skin_color.css'),
    ];

    $styleStack = array_merge(
        $defaultStyles,
        collect($styles ?? [])
            ->filter(fn ($style) => is_string($style) && $style !== '')
            ->unique()
            ->map(fn (string $style) => Str::startsWith($style, ['http://', 'https://', '//'])
                ? $style
                : asset(ltrim($style, '/'))
            )
            ->values()
            ->all()
    );

    $defaultScripts = ['js/vendors.min.js'];

    $scriptStack = array_merge(
        $defaultScripts,
        collect($scripts ?? [])
            ->filter(fn ($script) => is_string($script) && $script !== '')
            ->unique()
            ->map(fn (string $script) => Str::startsWith($script, ['http://', 'https://', '//'])
                ? $script
                : asset(ltrim($script, '/'))
            )
            ->values()
            ->all()
    );

    $inlineScripts = collect($inlineScripts ?? [])
        ->filter(fn ($script) => is_string($script) && trim($script) !== '')
        ->values();
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $pageTitle }}</title>
    <link rel="icon" href="{{ Vite::asset('resources/images/favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @foreach($styleStack as $href)
        <link rel="stylesheet" href="{{ $href }}">
    @endforeach

    @stack('styles')
</head>
<body class="{{ $bodyClass }}">
<main class="turnero-main" role="main">
    @yield('content')
</main>

@foreach($scriptStack as $script)
    <script src="{{ $script }}"></script>
@endforeach

@stack('scripts')

@foreach($inlineScripts as $inlineScript)
    <script>{!! $inlineScript !!}</script>
@endforeach
</body>
</html>
