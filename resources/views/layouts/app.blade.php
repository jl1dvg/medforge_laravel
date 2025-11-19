@php
    use App\Support\LegacyPermissions;
    use Illuminate\Support\Str;

    if (! function_exists('medforge_profile_photo_url')) {
        function medforge_profile_photo_url(?string $path): ?string
        {
            if (! $path) {
                return null;
            }

            if (Str::startsWith($path, ['http://', 'https://'])) {
                return $path;
            }

            return asset(ltrim($path, '/'));
        }
    }

    if (! function_exists('medforge_initials')) {
        function medforge_initials(string $name): string
        {
            $trimmed = trim($name);

            if ($trimmed === '') {
                return 'U';
            }

            $parts = preg_split('/\s+/u', $trimmed) ?: [];

            if (count($parts) === 1) {
                return Str::upper(Str::substr($parts[0], 0, 2));
            }

            $first = Str::substr($parts[0], 0, 1);
            $last = Str::substr($parts[count($parts) - 1], 0, 1);

            return Str::upper($first.$last);
        }
    }

    $pageTitle = trim($__env->yieldContent('title', ''));
    $fullTitle = $pageTitle !== '' ? "{$pageTitle} · MedForge" : 'MedForge';
    $bodyClass = trim($__env->yieldContent('body-class', '')) ?: 'hold-transition light-skin sidebar-mini theme-primary fixed';

    $authUser = auth()->user();
    $displayName = $authUser?->nombre ?? $authUser?->username ?? 'Usuario';
    $roleName = $authUser?->role->name ?? 'Usuario';
    $profilePhotoUrl = medforge_profile_photo_url($authUser?->profile_photo);
    $normalizedPermissions = $authUser ? LegacyPermissions::normalize($authUser->permisos ?? []) : [];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    <title>{{ $fullTitle }}</title>

    <link rel="stylesheet" href="{{ asset('css/vendors_css.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/horizontal-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skin_color.css') }}">
    @stack('styles')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js" defer></script>
</head>
<body class="{{ $bodyClass }}">
<div class="wrapper">
    @include('layouts.partials.header', [
        'displayName' => $displayName,
        'roleName' => $roleName,
        'profilePhotoUrl' => $profilePhotoUrl,
        'email' => $authUser?->email,
        'initials' => medforge_initials($displayName),
    ])

    @include('layouts.partials.notification-panel')

    @include('layouts.partials.sidebar', [
        'permissions' => $normalizedPermissions,
    ])

    <div class="content-wrapper">
        <div class="container-full">
            @yield('content')
        </div>
    </div>

    @include('layouts.partials.footer')
</div>

@php
    $defaultScripts = [
        'js/vendors.min.js',
        'js/pages/chat-popup.js',
        'assets/icons/feather-icons/feather.min.js',
        'js/jquery.smartmenus.js',
        'js/menus.js',
        'js/pages/global-search.js',
        'js/template.js',
    ];
@endphp

@foreach($defaultScripts as $script)
    <script src="{{ asset($script) }}"></script>
@endforeach

@stack('scripts')
</body>
</html>
