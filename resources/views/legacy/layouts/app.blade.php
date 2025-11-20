@php
    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\Vite;
    use Illuminate\Support\Str;

    $title = trim($__env->yieldContent('title') ?? '');
    $pageTitle = $title !== '' ? "MedForge - {$title}" : 'MedForge';

    $bodyClass = trim($__env->yieldContent('body_class') ?? '')
        ?: 'hold-transition light-skin sidebar-mini theme-primary fixed';

    $defaultStyles = [
        asset('css/vendors_css.css'),
        asset('css/horizontal-menu.css'),
        asset('css/style.css'),
        asset('css/skin_color.css'),
    ];

    $additionalStyles = collect($styles ?? [])
        ->filter(fn ($style) => is_string($style) && $style !== '')
        ->unique()
        ->map(fn (string $style) => Str::startsWith($style, ['http://', 'https://', '//'])
            ? $style
            : asset(ltrim($style, '/'))
        )
        ->values()
        ->all();

    $styleStack = array_merge($defaultStyles, $additionalStyles);

    $defaultScripts = [
        'js/vendors.min.js',
        'js/pages/chat-popup.js',
        'assets/icons/feather-icons/feather.min.js',
        'js/jquery.smartmenus.js',
        'js/menus.js',
        'js/pages/global-search.js',
        'js/template.js',
    ];

    $additionalScripts = collect($scripts ?? [])
        ->filter(fn ($script) => is_string($script) && $script !== '')
        ->unique()
        ->map(fn (string $script) => Str::startsWith($script, ['http://', 'https://', '//'])
            ? $script
            : asset(ltrim($script, '/'))
        )
        ->values()
        ->all();

    $scriptStack = array_merge($defaultScripts, $additionalScripts);

    $inlineScripts = collect($inlineScripts ?? [])
        ->filter(fn ($script) => is_string($script) && trim($script) !== '')
        ->values();

    $showShell = filter_var($showShell ?? $__env->yieldContent('showShell') ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    $showShell = $showShell ?? true;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ Vite::asset('resources/images/favicon.ico') }}">
    <title>{{ $pageTitle }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @foreach($styleStack as $href)
        <link rel="stylesheet" href="{{ $href }}">
    @endforeach

    @stack('styles')
</head>

<body class="{{ $bodyClass }}">
<div class="wrapper">
    @if($showShell)
        @include('legacy.partials.header', ['currentUser' => $currentUser ?? null, 'username' => $username ?? null])
        @include('legacy.partials.notification-panel')
        @include('legacy.partials.navbar')

        <div class="content-wrapper">
            <div class="container-full">
                @yield('content')
            </div>
        </div>

        @include('legacy.partials.footer')
    @else
        <main class="auth-wrapper">
            @yield('content')
        </main>
    @endif
</div>

@foreach($scriptStack as $script)
    <script src="{{ $script }}"></script>
@endforeach

@stack('scripts')

@foreach($inlineScripts as $inlineScript)
    <script>{!! $inlineScript !!}</script>
@endforeach

@if($showShell)
    @php
        $notificationChannels = [
            'email' => false,
            'sms' => false,
            'daily_summary' => false,
        ];

        $pusherEnabled = false;
        $pusherKey = '';
    @endphp
    <script type="module">
        import {createNotificationPanel} from '{{ asset('js/pages/solicitudes/notifications/panel.js') }}';

        window.MEDF = window.MEDF || {};
        const panel = window.MEDF.notificationPanel || createNotificationPanel({
            panelId: 'kanbanNotificationPanel',
            backdropId: 'notificationPanelBackdrop',
            toggleSelector: '[data-notification-panel-toggle]'
        });
        window.MEDF.notificationPanel = panel;

        window.MEDF.defaultNotificationChannels = @json($notificationChannels);
        panel.setChannelPreferences(window.MEDF.defaultNotificationChannels);

        window.MEDF.pusherIntegration = {
            enabled: {{ $pusherEnabled ? 'true' : 'false' }},
            hasKey: {{ $pusherKey !== '' ? 'true' : 'false' }}
        };

        if (!window.MEDF.pusherIntegration.enabled) {
            panel.setIntegrationWarning('Las notificaciones en tiempo real están desactivadas en Configuración → Notificaciones.');
        } else if (!window.MEDF.pusherIntegration.hasKey) {
            panel.setIntegrationWarning('No se configuró la APP Key de Pusher en los ajustes.');
        } else {
            panel.setIntegrationWarning('');
        }
    </script>
@endif
</body>
</html>
