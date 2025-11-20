@extends('layouts.app')

@section('title', $pageTitle ?? 'Dashboard')

@php
    $fechas_json = json_encode($procedimientos_dia['fechas'] ?? []);
    $procedimientos_dia_json = json_encode($procedimientos_dia['totales'] ?? []);
    $membretes_json = json_encode($top_procedimientos['membretes'] ?? []);
    $procedimientos_membrete_json = json_encode($top_procedimientos['totales'] ?? []);
    $afiliaciones_json = json_encode($estadisticas_afiliacion['afiliaciones'] ?? []);
    $procedimientos_por_afiliacion_json = json_encode($estadisticas_afiliacion['totales'] ?? []);
    $solicitudes_funnel_json = json_encode($solicitudes_funnel ?? []);
    $crm_backlog_json = json_encode($crm_backlog ?? []);
    $revision_estados_json = json_encode($revision_estados ?? []);
    $date_range_json = json_encode($date_range ?? []);
@endphp

@section('content')
    <section class="content legacy-dashboard">
        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="box mb-0">
                    <div class="box-body">
                        <form method="get" class="row g-3 align-items-end">
                            <div class="col-md-4 col-lg-3">
                                <label for="start_date" class="form-label">Desde</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $date_range['start'] ?? '' }}">
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <label for="end_date" class="form-label">Hasta</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $date_range['end'] ?? '' }}">
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <label class="form-label d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa fa-filter me-2"></i>Aplicar filtros
                                </button>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <label class="form-label d-block">&nbsp;</label>
                                <a href="{{ route('dashboard') }}" class="btn btn-light w-100">
                                    <i class="fa fa-undo me-2"></i>Limpiar
                                </a>
                            </div>
                        </form>
                        <p class="text-muted fs-12 mb-0 mt-2">
                            Mostrando datos del periodo: <strong>{{ $date_range['label'] ?? '' }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            @include('dashboard.legacy.components.top', ['kpi_cards' => $kpi_cards])

            <div class="col-xl-6 col-12">
                <div class="box">
                    <div class="box-header">
                        <h4 class="box-title">Embudo de solicitudes quirúrgicas</h4>
                        <span class="badge bg-light text-primary">Conversión {{ $solicitudes_funnel['totales_porcentajes']['conversion_agendada'] ?? 0 }}%</span>
                    </div>
                    <div class="box-body">
                        <div id="solicitudes_funnel_chart"></div>
                        <div class="d-flex flex-wrap gap-3 justify-content-between text-muted fs-12 mt-3">
                            <span><strong>{{ number_format($solicitudes_funnel['totales']['registradas'] ?? 0) }}</strong> solicitudes registradas</span>
                            <span><strong>{{ number_format($solicitudes_funnel['totales']['agendadas'] ?? 0) }}</strong> con turno</span>
                            <span><strong>{{ number_format($solicitudes_funnel['totales']['con_cirugia'] ?? 0) }}</strong> con cirugía</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-12">
                <div class="box">
                    <div class="box-header">
                        <h4 class="box-title">Backlog CRM</h4>
                        <span class="badge bg-light text-primary">Avance {{ $crm_backlog['avance'] ?? 0 }}%</span>
                    </div>
                    <div class="box-body">
                        <div id="crm_backlog_chart"></div>
                        <ul class="list-inline mt-3 mb-0 text-muted fs-12">
                            <li class="list-inline-item me-4"><strong>{{ number_format($crm_backlog['pendientes'] ?? 0) }}</strong> pendientes</li>
                            <li class="list-inline-item me-4"><strong>{{ number_format($crm_backlog['vencidas'] ?? 0) }}</strong> vencidas</li>
                            <li class="list-inline-item"><strong>{{ number_format($crm_backlog['vencen_hoy'] ?? 0) }}</strong> vencen hoy</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-12">
                <div class="box">
                    <div class="box-header">
                        <h4 class="box-title">Procedimientos más realizados</h4>
                    </div>
                    <div class="box-body">
                        <div id="patient_statistics"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-12">
                <div class="box">
                    <div class="box-header">
                        <h4 class="box-title">Estado de protocolos</h4>
                    </div>
                    <div class="box-body">
                        <div id="revision_estado_chart"></div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Cirugías recientes</h4>
                    </div>
                    <div class="box-body no-padding">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="bg-info-light">
                                <tr>
                                    <th>No</th>
                                    <th>Fecha</th>
                                    <th>HC</th>
                                    <th>Paciente</th>
                                    <th>Afiliación</th>
                                    <th>Ciudad</th>
                                    <th>Procedimiento</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($cirugias_recientes['data'] ?? [] as $index => $cirugia)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ date('d/m/Y', strtotime($cirugia['fecha_inicio'])) }}</td>
                                        <td>{{ $cirugia['hc_number'] }}</td>
                                        <td>{{ ($cirugia['lname'] ?? '') . ' ' . ($cirugia['lname2'] ?? '') . ' ' . ($cirugia['fname'] ?? '') }}</td>
                                        <td>{{ $cirugia['afiliacion'] ?? '—' }}</td>
                                        <td>{{ $cirugia['ciudad'] ?? '—' }}</td>
                                        <td>{{ $cirugia['membrete'] ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">No hay cirugías en el rango seleccionado.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6 col-12">
                <div class="box">
                    <div class="box-header">
                        <h4 class="box-title">Solicitudes quirúrgicas recientes</h4>
                    </div>
                    <div class="box-body p-0">
                        <div class="media-list media-list-hover media-list-divided">
                            @foreach($solicitudes_quirurgicas['solicitudes'] ?? [] as $solicitud)
                                <a class="media media-single">
                                    <h5 class="text-fade"><time datetime="{{ $solicitud['fecha'] }}">{{ date('d/m', strtotime($solicitud['fecha'])) }}</time></h5>
                                    <div class="media-body">
                                        <p>{{ $solicitud['procedimiento'] ?? 'Procedimiento' }}</p>
                                        <p class="text-fade">{{ $solicitud['lname'] ?? '' }} {{ $solicitud['fname'] ?? '' }} · HC {{ $solicitud['hc_number'] ?? '' }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="text-center py-2 text-muted fs-12">Total: {{ $solicitudes_quirurgicas['total'] ?? 0 }} solicitudes</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6 col-12">
                <div class="box">
                    <div class="box-header">
                        <h4 class="box-title">Top doctores (3 meses)</h4>
                    </div>
                    <div class="box-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <tbody>
                                @forelse($doctores_top ?? [] as $doctor)
                                    <tr>
                                        <td>{{ $doctor['cirujano_1'] }}</td>
                                        <td class="text-end fw-600">{{ number_format($doctor['total'] ?? 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-3">Sin datos recientes.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-12 col-12">
                <div class="box">
                    <div class="box-header">
                        <h4 class="box-title">Afiliaciones del mes</h4>
                    </div>
                    <div class="box-body">
                        <div id="afiliacion_chart"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-12">
                <div class="box">
                    <div class="box-header">
                        <h4 class="box-title">Plantillas recientes</h4>
                        <div class="box-controls">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-light btn-filter active" data-filter="all">Todas</button>
                                <button type="button" class="btn btn-sm btn-light btn-filter" data-filter="creado">Creadas</button>
                                <button type="button" class="btn btn-sm btn-light btn-filter" data-filter="modificado">Modificadas</button>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <p id="plantilla-count" class="text-muted fs-12 mb-3"></p>
                        <div class="row g-3">
                            @forelse($plantillas ?? [] as $plantilla)
                                <div class="col-md-6 plantilla-card" data-tipo="{{ strtolower($plantilla['tipo']) }}">
                                    <div class="box shadow-sm h-100">
                                        <div class="box-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="mb-0">{{ $plantilla['membrete'] ?? 'Plantilla' }}</h5>
                                                <span class="badge bg-light text-primary text-uppercase fs-11">{{ $plantilla['tipo'] }}</span>
                                            </div>
                                            <p class="text-muted mb-2">{{ $plantilla['cirugia'] ?? '—' }}</p>
                                            <p class="text-muted fs-12 mb-0">{{ date('d/m/Y', strtotime($plantilla['fecha'])) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">No hay plantillas registradas.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-12">
                <div class="box h-100">
                    <div class="box-header">
                        <h4 class="box-title">Diagnósticos frecuentes</h4>
                    </div>
                    <div class="box-body">
                        <ul class="list-group">
                            @forelse($diagnosticos_frecuentes ?? [] as $diagnostico => $total)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $diagnostico }}</span>
                                    <span class="badge bg-primary rounded-pill">{{ $total }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">No hay datos disponibles.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor_components/apexcharts-bundle/dist/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/OwlCarousel2/dist/owl.carousel.js') }}"></script>
    <script>
        const dashboardDateRange = {!! $date_range_json !!};
        const fechas = {!! $fechas_json !!};
        const procedimientos_dia = {!! $procedimientos_dia_json !!};
        const membretes = {!! $membretes_json !!};
        const procedimientos_membrete = {!! $procedimientos_membrete_json !!};
        const afiliaciones = {!! $afiliaciones_json !!};
        const procedimientos_por_afiliacion = {!! $procedimientos_por_afiliacion_json !!};
        const solicitudesFunnel = {!! $solicitudes_funnel_json !!};
        const crmBacklog = {!! $crm_backlog_json !!};
        const revisionEstados = {!! $revision_estados_json !!};
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterButtons = document.querySelectorAll('.btn-filter');
            const cards = document.querySelectorAll('.plantilla-card');
            const countSpan = document.getElementById('plantilla-count');

            const updateCount = () => {
                const visibles = [...cards].filter(c => c.style.display !== 'none').length;
                if (countSpan) {
                    countSpan.textContent = `Mostrando ${visibles} plantilla${visibles !== 1 ? 's' : ''}`;
                }
            };

            const filterCards = (type) => {
                cards.forEach(card => {
                    const tipo = (card.dataset.tipo || '').toLowerCase();
                    if (type === 'all' || tipo === type) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
                updateCount();
            };

            filterButtons.forEach(button => {
                button.addEventListener('click', function () {
                    filterButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    filterCards(this.dataset.filter);
                });
            });

            filterCards('all');
        });
    </script>
@endpush
