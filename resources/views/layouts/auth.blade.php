<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MedForge · @yield('title', 'Iniciar sesión')</title>
    <link rel="icon" href="{{ Vite::asset('resources/images/favicon.ico') }}">
    @vite([
        'resources/css/app.css',
        'resources/css/legacy.css',
        'resources/css/legacy-auth.css',
        'resources/js/app.js',
        'resources/js/legacy/index.js',
    ])
    @stack('styles')
</head>
<body class="@yield('body-class', 'hold-transition auth-body bg-light')">
<main>
    @yield('content')
</main>
@stack('scripts')
</body>
</html>
