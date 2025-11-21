@extends('layouts.app')

@section('title', 'Paciente ' . $hcNumber)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/horizontal-timeline/css/style.css') }}">
    <style>
        .patient-hero__bg {
            min-height: 140px;
            background-size: cover;
            background-position: center;
        }
        .patient-hero__avatar img {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 16px;
            border: 4px solid #fff;
        }
        .timeline-vertical .item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f2f2f2;
        }
        .timeline-vertical .bullet {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            margin-top: 8px;
        }
    </style>
@endpush

@section('content')
    @php
        $fullName = trim(($patient->fname ?? '') . ' ' . ($patient->mname ?? '') . ' ' . ($patient->lname ?? '') . ' ' . ($patient->lname2 ?? ''));
        $insurance = strtolower($patient->afiliacion ?? '');
        $background = match (true) {
            str_contains($insurance, 'issfa') => 'assets/logos_seguros/2.png',
            str_contains($insurance, 'isspol') => 'assets/logos_seguros/3.png',
            str_contains($insurance, 'msp') => 'assets/logos_seguros/4.png',
            str_contains($insurance, 'seguro general') => 'assets/logos_seguros/1.png',
            default => 'assets/logos_seguros/5.png',
        };
        $gender = strtolower($patient->sexo ?? '');
        $avatar = str_contains($gender, 'masculino') ? 'images/avatar/male.png' : 'images/avatar/female.png';
    @endphp

    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h3 class="page-title">Paciente {{ $hcNumber }}</h3>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('pacientes.index') }}">Pacientes</a></li>
                            <li class="breadcrumb-item active" aria-current="page">HC {{ $hcNumber }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row g-3 mb-3">
            <div class="col-xl-6 col-12">
                <div class="box patient-hero">
                    <div class="patient-hero__bg" style="background-image:url('{{ asset($background) }}')"></div>
                    <div class="box-body">
                        <button class="btn btn-warning mb-3" type="button" data-bs-toggle="modal" data-bs-target="#modalEditarPaciente">Editar datos</button>
                        <div class="d-md-flex align-items-center gap-4">
                            <div class="patient-hero__avatar text-center">
                                <img src="{{ asset($avatar) }}" alt="Avatar">
                                <div class="text-center my-2">
                                    <p class="mb-0 text-muted">Afiliación</p>
                                    <h4 class="mb-0">{{ $patient->afiliacion ?? '—' }}</h4>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h4 class="fw-semibold mb-1">{{ $fullName }}</h4>
                                <h5 class="fw-light mb-2">C. I.: {{ $patient->hc_number }}</h5>
                                <p class="mb-1"><i class="fa fa-clock-o"></i> Edad: {{ $patientAge ? $patientAge . ' años' : '—' }}</p>
                                <p class="mb-1"><i class="fa fa-phone"></i> {{ $patient->celular ?? '---' }}</p>
                                <p class="mb-1"><i class="fa fa-map-marker"></i> {{ $patient->ciudad ?? '—' }}</p>
                                @php
                                    $badge = match ($coverage) {
                                        'Con Cobertura' => 'bg-success',
                                        'Sin Cobertura' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ $coverage }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-12">
                <div class="box h-100">
                    <div class="box-header with-border">
                        <h4 class="box-title mb-0">Solicitudes y prefacturas</h4>
                    </div>
                    <div class="box-body">
                        @if(empty($timelineItems))
                            <p class="text-muted mb-0">Sin solicitudes recientes.</p>
                        @else
                            <div class="timeline-vertical">
                                @foreach($timelineItems as $item)
                                    @php
                                        $color = match ($item['origen']) {
                                            'Prefactura' => 'bg-info',
                                            default => 'bg-primary',
                                        };
                                        $fechaLinea = $item['fecha'] ? \Carbon\Carbon::parse($item['fecha'])->format('d/m/Y') : '—';
                                        $isSolicitud = ($item['origen'] ?? '') === 'Solicitud';
                                    @endphp
                                    <div class="item">
                                        <span class="bullet {{ $color }}"></span>
                                        <div class="flex-grow-1">
                                            <a href="#" class="fw-semibold">{{ $item['nombre'] }}</a>
                                            <p class="mb-0 text-muted small">{{ ucfirst(strtolower($item['origen'])) }} · {{ $fechaLinea }}</p>
                                        </div>
                                        @if($isSolicitud)
                                            <div class="dropdown">
                                                <a class="px-10 pt-5" href="#" data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item flexbox"
                                                       href="#"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#modalSolicitud"
                                                       data-hc="{{ $patient->hc_number }}"
                                                       data-form-id="{{ $item['form_id'] ?? '' }}">
                                                        <span>Ver detalles</span>
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-6 col-12">
                <div class="box">
                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                        <h4 class="box-title mb-0">Descargar archivos</h4>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">Filtro</button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#" onclick="filterDocuments('todos');return false;">Todos</a>
                                <a class="dropdown-item" href="#" onclick="filterDocuments('ultimo_mes');return false;">Último mes</a>
                                <a class="dropdown-item" href="#" onclick="filterDocuments('ultimos_3_meses');return false;">Últimos 3 meses</a>
                                <a class="dropdown-item" href="#" onclick="filterDocuments('ultimos_6_meses');return false;">Últimos 6 meses</a>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="media-list media-list-divided" id="document-list">
                            @forelse($documentos as $item)
                                @php
                                    $isProtocolo = $item['tipo'] === 'protocolo';
                                    $fecha = $item['fecha'] ? \Carbon\Carbon::parse($item['fecha']) : null;
                                @endphp
                                <div class="media media-single px-0" data-date="{{ $fecha?->format('Y-m-d') }}">
                                    <div class="ms-0 me-15 {{ $isProtocolo ? 'bg-success-light' : 'bg-primary-light' }} h-50 w-50 l-h-50 rounded text-center d-flex align-items-center justify-content-center">
                                        <span class="fs-24 {{ $isProtocolo ? 'text-success' : 'text-primary' }}">
                                            <i class="fa {{ $isProtocolo ? 'fa-file-pdf-o' : 'fa-file-text-o' }}"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <span class="title fw-500 fs-16 text-truncate">{{ $item['titulo'] }}</span>
                                        <span class="text-fade fw-500 fs-12">{{ $fecha?->format('d/m/Y') ?? '—' }}</span>
                                    </div>
                                    @if($isProtocolo)
                                        <a class="fs-18 text-gray" href="#" onclick="window.descargarPDFsSeparados?.('{{ $item['form_id'] }}','{{ $item['hc_number'] }}');return false;">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    @else
                                        <a class="fs-18 text-gray" href="{{ $solicitudPdfBaseUrl }}?hc_number={{ urlencode($item['hc_number']) }}&form_id={{ urlencode($item['form_id']) }}" target="_blank">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted mb-0">No hay documentos disponibles.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="box h-100">
                    <div class="box-header with-border">
                        <h4 class="box-title mb-0">Estadísticas de procedimientos</h4>
                    </div>
                    <div class="box-body">
                        <div id="paciente-estadisticas-chart"></div>
                        @if(empty($estadisticas))
                            <p class="text-muted mb-0">Sin datos suficientes para mostrar estadísticas.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-4 col-12">
                <div class="box h-100">
                    <div class="box-header with-border">
                        <h4 class="box-title mb-0">Diagnósticos frecuentes</h4>
                    </div>
                    <div class="box-body">
                        @if(empty($diagnosticos))
                            <p class="text-muted mb-0">Sin diagnósticos registrados.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($diagnosticos as $diagnostico)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $diagnostico['idDiagnostico'] }}</span>
                                        <small class="text-muted">{{ $diagnostico['fecha'] ?? '—' }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-12">
                <div class="box h-100">
                    <div class="box-header with-border">
                        <h4 class="box-title mb-0">Doctores asignados</h4>
                    </div>
                    <div class="box-body">
                        @if(empty($doctores))
                            <p class="text-muted mb-0">Sin registros.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($doctores as $doctor)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $doctor->doctor }}</span>
                                        <span class="badge bg-light text-dark">#{{ $doctor->form_id }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-12">
                <div class="box h-100">
                    <div class="box-header with-border">
                        <h4 class="box-title mb-0">Eventos clínicos</h4>
                    </div>
                    <div class="box-body">
                        @include('pacientes.components.timeline-horizontal', ['eventos' => $eventos])
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6 col-12">
                <div class="box">
                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                        <h4 class="box-title mb-0">Últimas consultas</h4>
                    </div>
                    <div class="box-body">
                        @if($consultations->isEmpty())
                            <p class="text-muted mb-0">Sin consultas registradas.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Diagnósticos</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($consultations as $consulta)
                                        <tr>
                                            <td>{{ $consulta['fecha'] ? \Carbon\Carbon::parse($consulta['fecha'])->format('d/m/Y') : '—' }}</td>
                                            <td>{{ $consulta['diagnosticos'] ?: '—' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="box">
                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                        <h4 class="box-title mb-0">Solicitudes recientes</h4>
                    </div>
                    <div class="box-body">
                        @if($solicitudes->isEmpty())
                            <p class="text-muted mb-0">Sin solicitudes registradas.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Procedimiento</th>
                                        <th>Tipo</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($solicitudes as $solicitud)
                                        <tr>
                                            <td>{{ $solicitud->created_at ? \Carbon\Carbon::parse($solicitud->created_at)->format('d/m/Y') : '—' }}</td>
                                            <td>{{ $solicitud->procedimiento }}</td>
                                            <td>{{ ucfirst(strtolower($solicitud->tipo ?? '')) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('pacientes.components.modal-solicitud')
    @include('pacientes.components.modal-editar')
@endsection

@push('scripts')
    <script>
        const statsData = @json($estadisticas);
        document.addEventListener('legacy:pacientes:scripts-ready', () => {
            const chartEl = document.getElementById('paciente-estadisticas-chart');
            if (chartEl && Object.keys(statsData).length && window.ApexCharts) {
                const chart = new ApexCharts(chartEl, {
                    series: Object.values(statsData),
                    chart: { type: 'donut' },
                    labels: Object.keys(statsData),
                    colors: ['#3246D3', '#00D0FF', '#ee3158', '#ffa800', '#05825f'],
                    legend: { position: 'bottom' },
                    plotOptions: { pie: { donut: { size: '45%' } } },
                });
                chart.render();
            }
        });
    </script>
    @vite('resources/js/pages/pacientes/show.js')
@endpush
