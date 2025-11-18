@extends('layouts.auth')

@section('title', 'Iniciar sesión')

@php
    $allowedStatusTypes = ['success', 'info', 'warning'];
    $statusBag = $status ?? null;
    $statusType = in_array($statusBag['type'] ?? null, $allowedStatusTypes, true)
        ? $statusBag['type']
        : 'warning';
@endphp

@section('content')
    <div class="container auth-wrapper py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="row g-4 align-items-stretch">
                    <div class="col-lg-6">
                        <section class="auth-hero h-100">
                            <span class="brand-pill">
                                <i class="fa-solid fa-shield-heart"></i> MedForge Identity
                            </span>
                            <h2>La forma más segura de volver a tu trabajo clínico</h2>
                            <p>Centraliza agendas, solicitudes y reportes en una sola plataforma diseñada para equipos de salud.</p>
                            <ul class="auth-hero-features">
                                <li><i class="fa-solid fa-chart-simple"></i> Panel clínico en tiempo real</li>
                                <li><i class="fa-solid fa-user-shield"></i> Accesos según permisos y roles</li>
                                <li><i class="fa-solid fa-laptop-medical"></i> Integración con turnero y kanban</li>
                            </ul>
                        </section>
                    </div>
                    <div class="col-lg-6">
                        <section class="auth-card h-100 d-flex flex-column justify-content-center">
                            <div class="mb-3">
                                <h1>Bienvenido de nuevo</h1>
                                <p class="description">Ingresa tus credenciales para continuar con tu jornada.</p>
                            </div>
                            @if (!empty($statusBag))
                                <div class="status-badge {{ 'status-' . $statusType }}">
                                    <i class="fa-solid fa-circle-info"></i>
                                    <span>{{ $statusBag['message'] ?? '' }}</span>
                                </div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger mt-3 mb-0">
                                    {{ $errors->first('username') ?? $errors->first('password') ?? __('auth.failed') }}
                                </div>
                            @endif
                            <form action="{{ route('login.store') }}" method="post" class="mt-4" autocomplete="off" novalidate>
                                @csrf
                                <div class="mb-3 form-floating position-relative">
                                    <input type="text"
                                           id="username"
                                           name="username"
                                           class="form-control @error('username') is-invalid @enderror"
                                           placeholder="Usuario"
                                           value="{{ old('username') }}"
                                           required
                                           autofocus>
                                    <label for="username">Usuario o correo</label>
                                    <span class="input-icon">
                                        <i class="fa-regular fa-user"></i>
                                    </span>
                                </div>
                                <div class="mb-2 form-floating position-relative">
                                    <input type="password"
                                           id="password"
                                           name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Contraseña"
                                           required>
                                    <label for="password">Contraseña</label>
                                    <button type="button" class="btn btn-link password-toggle input-icon" data-target="#password">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                                    <div class="form-check m-0">
                                        <input class="form-check-input" type="checkbox" value="1" id="remember-me" name="remember" disabled>
                                        <label class="form-check-label" for="remember-me">
                                            Recordarme
                                        </label>
                                    </div>
                                    <a class="text-decoration-none fw-semibold" href="mailto:soporte@medforge.io">
                                        ¿Olvidaste la contraseña?
                                    </a>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
                            </form>
                            <p class="auth-footnote text-center">
                                ¿Necesitas ayuda con tu usuario? <a href="mailto:soporte@medforge.io">Contacta a soporte</a>
                            </p>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.password-toggle').forEach((toggle) => {
                toggle.addEventListener('click', () => {
                    const targetSelector = toggle.getAttribute('data-target');
                    const target = document.querySelector(targetSelector);
                    if (!target) {
                        return;
                    }
                    const isPassword = target.getAttribute('type') === 'password';
                    target.setAttribute('type', isPassword ? 'text' : 'password');
                    const icon = toggle.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                });
            });
        });
    </script>
@endpush
