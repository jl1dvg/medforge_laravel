<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedForge · Panel</title>
    <link rel="stylesheet" href="{{ asset('css/vendors_css.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-RXf+QSDCUQs5uwRKa0p9ju9fNvZkNHE1c5CkS4Ix2RDkz4V3N6E0jIwGM7bIJF5Gx4nFP9YV7QvMG4z1AnPdhw=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skin_color.css') }}">
</head>
<body class="hold-transition light-skin sidebar-mini theme-primary fixed">
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="text-center p-4 shadow rounded-3 bg-white" style="max-width: 480px;">
        <p class="text-uppercase text-muted fw-semibold mb-2">MedForge</p>
        <h1 class="h3 mb-3">Dashboard en construcción</h1>
        <p class="mb-4">
            Hola, <strong>{{ $user?->nombre ?? $user?->username ?? 'Usuario' }}</strong>.
            Estamos migrando los módulos a la nueva estructura de Laravel.
        </p>
        <p class="text-muted small mb-4">
            Este panel reemplazará gradualmente al layout legacy mientras se completen los módulos.
        </p>
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Cerrar sesión
            </button>
        </form>
    </div>
</div>
</body>
</html>
