<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MedForge · @yield('title', 'Iniciar sesión')</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/vendors_css.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-RXf+QSDCUQs5uwRKa0p9ju9fNvZkNHE1c5CkS4Ix2RDkz4V3N6E0jIwGM7bIJF5Gx4nFP9YV7QvMG4z1AnPdhw=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    @stack('styles')
</head>
<body class="@yield('body-class', 'hold-transition auth-body bg-light')">
<main>
    @yield('content')
</main>
@stack('scripts')
</body>
</html>
