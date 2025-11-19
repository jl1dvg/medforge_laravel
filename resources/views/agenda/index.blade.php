@extends('layouts.app')

@section('title', 'Agenda de procedimientos')

@php
    use App\Support\AgendaViewHelper;
@endphp

@section('content')
<section class="content-header">
    <div class="d-flex align-items-center">
        <div class="me-auto">
            <h3 class="page-title">Agenda de procedimientos</h3>
            <div class="d-inline-block align-items-center">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Agenda</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="ms-auto text-muted fw-600">
            {{ $procedures->count() }} resultados
        </div>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Filtros</h4>
                </div>
                <div class="box-body">
                    <form method="get" class="row g-3">
                        <div class="col-sm-6 col-md-3">
                            <label for="fecha_inicio" class="form-label fw-600">Fecha desde</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ $filters['fecha_inicio'] }}" class="form-control">
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label for="fecha_fin" class="form-label fw-600">Fecha hasta</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" value="{{ $filters['fecha_fin'] }}" class="form-control">
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label for="doctor" class="form-label fw-600">Doctor</label>
                            <select id="doctor" name="doctor" class="form-select">
                                <option value="">Todos</option>
                                @foreach ($availableDoctors as $doctor)
                                    <option value="{{ $doctor }}" @selected($filters['doctor'] === $doctor)>{{ $doctor }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label for="estado" class="form-label fw-600">Estado</label>
                            <select id="estado" name="estado" class="form-select">
                                <option value="">Todos</option>
                                @foreach ($availableStates as $state)
                                    <option value="{{ $state }}" @selected($filters['estado'] === $state)>{{ $state }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label for="sede" class="form-label fw-600">Sede</label>
                            <select id="sede" name="sede" class="form-select">
                                <option value="">Todas</option>
                                @foreach ($availableLocations as $location)
                                    @php
                                        $value = $location['id_sede'] ?? $location['sede_departamento'];
                                        $labelParts = array_filter([
                                            $location['sede_departamento'] ?? null,
                                            isset($location['id_sede']) && $location['id_sede'] !== null ? '#'.$location['id_sede'] : null,
                                        ]);
                                        $label = $labelParts !== [] ? implode(' ', $labelParts) : 'Sin nombre';
                                    @endphp
                                    <option value="{{ $value }}" @selected($filters['sede'] === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="solo_con_visita" name="solo_con_visita" @checked($filters['solo_con_visita'])>
                                <label class="form-check-label" for="solo_con_visita">Solo con encuentro asignado</label>
                            </div>
                        </div>
                        <div class="col-12 d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-magnify"></i> Filtrar
                            </button>
                            <a href="{{ route('agenda.index') }}" class="btn btn-light">Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Procedimientos proyectados</h4>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="bg-primary-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Form ID</th>
                                <th>Paciente</th>
                                <th>Procedimiento</th>
                                <th>Doctor</th>
                                <th>Estado</th>
                                <th>Sede</th>
                                <th>Encuentro</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($procedures as $registro)
                                @php
                                    $fechaAgenda = $registro->agenda_date ? \Carbon\Carbon::parse($registro->agenda_date)->format('d/m/Y') : '—';
                                    $horaAgenda = $registro->agenda_time ?? '—';
                                    $paciente = $registro->patient?->full_name ?? 'Sin registro';
                                    $sedeNombre = $registro->sede_departamento ?: ($registro->id_sede ?? '—');
                                    $estado = $registro->estado_agenda ?? 'Sin estado';
                                    $hcNumber = $registro->hc_number;
                                @endphp
                                <tr>
                                    <td>{{ $fechaAgenda }}</td>
                                    <td>{{ $horaAgenda }}</td>
                                    <td>
                                        <span class="badge bg-info-light text-primary fw-600">{{ $registro->form_id }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-600 text-dark">{{ $paciente }}</div>
                                        <div class="text-muted small">HC {{ $hcNumber ?? '—' }}</div>
                                        @if ($hcNumber)
                                            <div>
                                                <a class="small" href="{{ route('pacientes.show', $hcNumber) }}">Ver ficha</a>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $registro->procedimiento_proyectado ?? '—' }}</td>
                                    <td>{{ $registro->doctor ?? '—' }}</td>
                                    <td>
                                        <span class="{{ AgendaViewHelper::badgeClass($estado) }}">{{ $estado }}</span>
                                    </td>
                                    <td>{{ $sedeNombre }}</td>
                                    <td>
                                        @if ($registro->visita_id)
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('agenda.visits.show', $registro->visita_id) }}">
                                                <i class="mdi mdi-link-variant"></i> Ver encuentro
                                            </a>
                                        @else
                                            <span class="badge bg-secondary">Sin encuentro</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        No se encontraron procedimientos proyectados para los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
