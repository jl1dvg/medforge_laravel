<header class="main-header">
    <div class="d-flex align-items-center logo-box justify-content-start">
        <a href="{{ route('dashboard') }}" class="logo">
            <div class="logo-mini w-50">
                <span class="light-logo"><img src="{{ asset('images/logo-light-text.png') }}" alt="logo"></span>
                <span class="dark-logo"><img src="{{ asset('images/logo-light-text.png') }}" alt="logo"></span>
            </div>
            <div class="logo-lg">
                <span class="light-logo"><img src="{{ asset('images/logo-light-text.png') }}" alt="logo"></span>
                <span class="dark-logo"><img src="{{ asset('images/logo-light-text.png') }}" alt="logo"></span>
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
                                    <input type="search" class="form-control" id="global-search-input" name="q" placeholder="Buscar en el sistema..." aria-label="Buscar en el sistema" aria-controls="global-search-results" aria-expanded="false" autocomplete="off">
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
                    <a href="#" class="waves-effect waves-light dropdown-toggle w-auto l-h-12 bg-transparent p-0 no-shadow" data-bs-toggle="dropdown" title="Usuario">
                        <div class="d-flex pt-1 align-items-center">
                            <div class="text-end me-10">
                                <p class="pt-5 fs-14 mb-0 fw-700 text-primary">{{ $displayName }}</p>
                                <span class="fs-12 text-muted">{{ $roleName }}</span>
                            </div>
                            <div class="dropdown-user d-inline-flex">
                                @if($profilePhotoUrl)
                                    <img src="{{ $profilePhotoUrl }}" class="avatar avatar-sm rounded-circle" alt="{{ $displayName }}">
                                @else
                                    <span class="avatar avatar-sm bg-primary text-white rounded-circle fw-bold d-inline-flex align-items-center justify-content-center">
                                        {{ $initials }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu animated flipInX">
                        <li class="user-header bg-img" data-overlay="3" style="background-image: url('{{ asset('images/user-info.jpg') }}')">
                            <div class="flexbox align-items-center">
                                @if($profilePhotoUrl)
                                    <img src="{{ $profilePhotoUrl }}" class="avatar avatar-xl rounded-circle bg-white" alt="{{ $displayName }}">
                                @else
                                    <span class="avatar avatar-xl bg-primary text-white rounded-circle fw-bold d-inline-flex align-items-center justify-content-center fs-24">
                                        {{ $initials }}
                                    </span>
                                @endif
                                <div>
                                    <p class="fs-16 mb-0 fw-600 text-white">{{ $displayName }}</p>
                                    <span class="text-white-50">{{ $email }}</span>
                                </div>
                            </div>
                        </li>
                        <li class="user-footer">
                            <div class="text-end">
                                <a href="{{ route('logout') }}" class="btn btn-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Cerrar sesión
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>
                <li class="btn-group nav-item">
                    <a href="#" class="waves-effect waves-light dropdown-toggle btn-info-light" title="Notifications">
                        <i class="icon-Notification"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                </li>
                <li class="btn-group nav-item">
                    <a href="#" data-toggle="control-sidebar" title="Setting" class="waves-effect full-screen waves-light btn-danger-light" data-notification-panel-toggle="true" aria-controls="kanbanNotificationPanel">
                        <i class="icon-Settings1"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>
