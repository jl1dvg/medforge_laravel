@extends('layouts.app')

@section('title', 'Módulos legacy')

@section('content')
    <section class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="content-title">Módulos legacy</h1>
                <p class="text-muted mb-0">Mapa rápido de servicios heredados y sus recursos compartidos.</p>
            </div>
            <span class="badge badge-primary fs-16">{{ count($modules) }} activos</span>
        </div>
    </section>

    <section class="content">
        <div class="row">
            @forelse($modules as $module)
                <div class="col-12 col-lg-6 col-xl-4">
                    <div class="box mb-4">
                        <div class="box-body">
                            <h4 class="box-title mb-10">{{ $module['name'] }}</h4>
                            <p class="text-muted mb-15">{{ $module['description'] }}</p>
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                <span class="badge badge-info">{{ count($module['routes']['web'] ?? []) }} rutas web</span>
                                <span class="badge badge-info">{{ count($module['routes']['api'] ?? []) }} rutas API</span>
                                <span class="badge badge-success">{{ count($module['assets']['scripts'] ?? []) }} scripts</span>
                            </div>
                            <a class="btn btn-primary" href="{{ route('legacy.modules.show', ['module' => $module['slug']]) }}">
                                Ver detalles
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        No existen módulos registrados todavía.
                    </div>
                </div>
            @endforelse
        </div>
    </section>
@endsection
