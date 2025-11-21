@php
    use Illuminate\Support\Facades\Vite;
    use Illuminate\Support\Str;

    $headerUser = is_array($currentUser ?? null) ? $currentUser : [];
    $headerDisplayName = $headerUser['display_name'] ?? ($username ?? 'Usuario');
    $headerRole = $headerUser['role_name'] ?? 'Usuario';

    $profilePath = $headerUser['profile_photo_url'] ?? ($headerUser['profile_photo'] ?? null);
    if ($profilePath && ! Str::startsWith($profilePath, ['http://', 'https://', '//'])) {
        $profilePath = asset(ltrim($profilePath, '/'));
    }

    $nameParts = collect(preg_split('/\s+/u', (string) $headerDisplayName) ?: [])
        ->filter()
        ->values();
    $headerInitials = $nameParts->count() === 0
        ? 'U'
        : ($nameParts->count() === 1
            ? Str::upper(Str::substr($nameParts->first(), 0, 2))
            : Str::upper(Str::substr($nameParts->first(), 0, 1).Str::substr($nameParts->last(), 0, 1))
        );
@endphp
<header class="main-header">
    <div class="d-flex align-items-center logo-box justify-content-start">
        <a href="/dashboard" class="logo">
            <div class="logo-mini w-50">
                <span class="light-logo"><img src="{{ Vite::asset('resources/images/logo-light-text.png') }}" alt="logo"></span>
                <span class="dark-logo"><img src="{{ Vite::asset('resources/images/logo-light-text.png') }}" alt="logo"></span>
            </div>
            <div class="logo-lg">
                <span class="light-logo"><img src="{{ Vite::asset('resources/images/logo-light-text.png') }}" alt="logo"></span>
                <span class="dark-logo"><img src="{{ Vite::asset('resources/images/logo-light-text.png') }}" alt="logo"></span>
            </div>
        </a>
    </div>
    <nav class="navbar navbar-static-top">
        <div class="app-menu">
            <ul class="header-megamenu nav">
                <li class="btn-group nav-item">
                    <a href="#" class="waves-effect waves-light nav-link push-btn btn-primary-light" data-toggle="push-menu" role="button">
                        <i class="icon-Menu"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                </li>
                <li class="btn-group d-lg-inline-flex d-none">
                    <div class="app-menu">
                        <div class="search-bx mx-5 position-relative" id="app-global-search">
                            <form class="global-search-form" autocomplete="off" novalidate>
                                <div class="input-group">
                                    <input
                                        type="search"
                                        class="form-control"
                                        id="global-search-input"
                                        name="q"
                                        placeholder="Buscar en el sistema..."
                                        aria-label="Buscar en el sistema"
                                        aria-controls="global-search-results"
                                        aria-expanded="false"
                                        autocomplete="off"
                                    >
                                    <div class="input-group-append">
                                        <button class="btn" type="submit" id="global-search-submit" aria-label="Ejecutar búsqueda">
                                            <i class="icon-Search"><span class="path1"></span><span class="path2"></span></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div class="global-search-panel card shadow-lg" id="global-search-results" role="listbox" aria-live="polite" hidden></div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="navbar-custom-menu r-side">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="waves-effect waves-light dropdown-toggle w-auto l-h-12 bg-transparent p-0 no-shadow" data-bs-toggle="dropdown" title="User">
                        <div class="d-flex pt-1">
                            <div class="text-end me-10">
                                <p class="pt-5 fs-14 mb-0 fw-700 text-primary">{{ $headerDisplayName }}</p>
                                <small class="fs-10 mb-0 text-uppercase text-mute">{{ $headerRole }}</small>
                            </div>
                            @if($profilePath)
                                <img src="{{ $profilePath }}" class="avatar rounded-10 h-40 w-40" style="object-fit: cover;" alt="{{ $headerDisplayName }}"/>
                            @else
                                <span class="avatar rounded-10 bg-primary-light h-40 w-40 d-inline-flex align-items-center justify-content-center text-primary fw-bold">
                                    {{ $headerInitials }}
                                </span>
                            @endif
                        </div>
                    </a>
                    <ul class="dropdown-menu animated flipInX">
                        <li class="user-body">
                            <a class="dropdown-item" href="extra_profile.html"><i class="mdi mdi-account text-muted me-2"></i> Profile</a>
                            <a class="dropdown-item" href="/auth/logout"><i class="mdi mdi-lock text-muted me-2"></i> Logout</a>
                        </li>
                    </ul>
                </li>
                <li class="btn-group nav-item d-lg-inline-flex d-none">
                    <a href="#" data-provide="fullscreen" class="waves-effect waves-light nav-link full-screen btn-warning-light" title="Full Screen">
                        <i class="icon-Position"></i>
                    </a>
                </li>
                <li class="dropdown notifications-menu">
                    <a href="#" class="waves-effect waves-light dropdown-toggle btn-info-light" data-bs-toggle="dropdown" title="Notifications">
                        <i class="icon-Notification"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                    <ul class="dropdown-menu animated bounceIn">
                        <li class="header">
                            <div class="p-20">
                                <div class="flexbox">
                                    <div>
                                        <h4 class="mb-0 mt-0">Notifications</h4>
                                    </div>
                                    <div>
                                        <a href="#" class="text-danger">Clear All</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <ul class="menu sm-scrol">
                                <li>
                                    <a href="#">
                                        <i class="fa fa-users text-info"></i> Curabitur id eros quis nunc suscipit blandit.
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-warning text-warning"></i> Duis malesuada justo eu sapien elementum, in semper diam posuere.
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-users text-danger"></i> Donec at nisi sit amet tortor commodo porttitor pretium a erat.
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-shopping-cart text-success"></i> In gravida mauris et nisi
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-user text-danger"></i> Praesent eu lacus in libero dictum fermentum.
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-user text-primary"></i> Nunc fringilla lorem
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-user text-success"></i> Nullam euismod dolor ut quam interdum, at scelerisque ipsum imperdiet.
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="#">View all</a>
                        </li>
                    </ul>
                </li>
                <li class="btn-group nav-item">
                    <a href="#" data-toggle="control-sidebar" title="Setting" class="waves-effect full-screen waves-light btn-danger-light">
                        <i class="icon-Settings-2"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                </li>
                <li class="btn-group nav-item">
                    <a href="#" title="Notificaciones" class="waves-effect waves-light btn-warning-light" data-notification-panel-toggle>
                        <i class="icon-Notification1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    </a>
                </li>
                <li class="btn-group nav-item d-lg-inline-flex d-none">
                    <a href="#" class="waves-effect waves-light nav-link rounded svg-bt-icon bg-info" title="Chat apps">
                        <i class="icon-Chat2"></i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>
