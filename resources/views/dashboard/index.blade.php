@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 70vh;">
        <div class="text-center p-4 shadow rounded-3 bg-white" style="max-width: 540px;">
            <p class="text-uppercase text-muted fw-semibold mb-2">MedForge</p>
            <h1 class="h3 mb-3">Panel en construcción</h1>
            <p class="mb-4">
                Estamos migrando los módulos a la arquitectura Laravel. Este dashboard servirá como base para los nuevos widgets.
            </p>
            <p class="text-muted small mb-4">
                Si necesitas acceder al panel anterior, avísanos para priorizar su migración.
            </p>
            <a href="mailto:soporte@medforge.io" class="btn btn-outline-primary">
                Solicitar ayuda
            </a>
        </div>
    </div>
@endsection
