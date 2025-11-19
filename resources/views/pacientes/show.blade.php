@extends('layouts.app')

@section('title', 'Paciente ' . $hcNumber)

@push('styles')
    <style>
        .patient-hero {
            position: relative;
            overflow: hidden;
        }
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
        .timeline-horizontal {
            display: flex;
            overflow-x: auto;
            gap: 1rem;
            padding-bottom: 8px;
        }
        .timeline-horizontal .card {
            min-width: 220px;
        }
    </style>
@endpush

@section('content')
    @php
        $fullName = trim(($patient->fname ?? '') . ' ' . ($patient->mname ?? '') . ' ' . ($patient->lname ?? '') . ' ' . ($patient->lname2 ?? ''));
        $insurance = strtolower($patient->afiliacion ?? '');
        $background = 'assets/logos_seguros/5.png';
        if (str_contains($insurance, 'issfa')) {
            $background = 'assets/logos_seguros/2.png';
        } elseif (str_contains($insurance, 'isspol')) {
            $background = 'assets/logos_seguros/3.png';
        } elseif (str_contains($insurance, 'msp')) {
            $background = 'assets/logos_seguros/4.png';
        } elseif (str_contains($insurance, 'seguro general')) {
            $background = 'assets/logos_seguros/1.png';
        }
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
                        <button class="btn btn-warning mb-3" type="button">Editar datos</button>
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
                        @forelse($timelineItems as $item)
                            @php
                                $color = match ($item['origen']) {
                                    'Prefactura' => 'bg-info',
                                    default => 'bg-primary',
                                };
                            @endphp
                            <div class="timeline-vertical">
                                <div class="item">
                                    <span class="bullet {{ $color }}"></span>
                                    <div>
                                        <a href="#" class="fw-semibold">{{ $item['nombre'] }}</a>
                                        <p class="mb-0 text-muted small">
                                            {{ ucfirst(strtolower($item['origen'])) }} · {{ $item['fecha'] ? \Carbon\Carbon::parse($item['fecha'])->format('d/m/Y') : '—' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Sin solicitudes recientes.</p>
                        @endforelse
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
                            @forelse($documentos as $documento)
                                @php
                                    $isProtocolo = $documento['tipo'] === 'protocolo';
                                    $fecha = $documento['fecha'] ? \Carbon\Carbon::parse($documento['fecha']) : null;
                                @endphp
                                <div class="media media-single px-0" data-date="{{ $fecha?->format('Y-m-d') }}">
                                    <div class="ms-0 me-15 {{ $isProtocolo ? 'bg-success-light' : 'bg-primary-light' }} h-50 w-50 l-h-50 rounded text-center d-flex align-items-center justify-content-center">
                                        <span class="fs-24 {{ $isProtocolo ? 'text-success' : 'text-primary' }}">
                                            <i class="fa {{ $isProtocolo ? 'fa-file-pdf-o' : 'fa-file-text-o' }}"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <span class="title fw-500 fs-16 text-truncate">{{ $documento['titulo'] }}</span>
                                        <span class="text-muted small">{{ $fecha?->format('d/m/Y') ?? '—' }}</span>
                                    </div>
                                    @if($isProtocolo)
                                        <a class="fs-18 text-gray" href="#" onclick="window.descargarPDFsSeparados?.('{{ $documento['form_id'] }}','{{ $documento['hc_number'] }}');return false;">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    @else
                                        <a class="fs-18 text-gray" href="{{ url('/views/reports/solicitud_quirurgica/solicitud_qx_pdf.php?hc_number=' . urlencode($documento['hc_number']) . '&form_id=' . urlencode($documento['form_id'])) }}" target="_blank">
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
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                        <h4 class="box-title mb-0">Eventos clínicos</h4>
                    </div>
                    <div class="box-body">
                        @if(empty($eventos))
                            <p class="text-muted mb-0">No hay datos disponibles para mostrar en el timeline.</p>
                        @else
                            <div class="timeline-horizontal">
                                @foreach($eventos as $evento)
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <h6 class="fw-semibold">{{ $evento->procedimiento_proyectado }}</h6>
                                            <p class="text-muted small mb-2">{{ $evento->fecha ? \Carbon\Carbon::parse($evento->fecha)->format('d/m/Y') : '—' }}</p>
                                            <p class="text-muted mb-0">{{ \Illuminate\Support\Str::limit($evento->contenido, 120) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        const statsData = @json($estadisticas);
        document.addEventListener('DOMContentLoaded', () => {
            const chartEl = document.getElementById('paciente-estadisticas-chart');
            if (chartEl && Object.keys(statsData).length) {
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

        function filterDocuments(range) {
            const items = document.querySelectorAll('#document-list .media');
            const now = new Date();
            items.forEach(item => {
                const dateAttr = item.getAttribute('data-date');
                if (!dateAttr) {
                    item.style.display = '';
                    return;
                }
                const itemDate = new Date(dateAttr);
                let diffMonths = (now.getFullYear() - itemDate.getFullYear()) * 12 + (now.getMonth() - itemDate.getMonth());
                if (range === 'todos') {
                    item.style.display = '';
                } else if (range === 'ultimo_mes') {
                    item.style.display = diffMonths <= 1 ? '' : 'none';
                } else if (range === 'ultimos_3_meses') {
                    item.style.display = diffMonths <= 3 ? '' : 'none';
                } else if (range === 'ultimos_6_meses') {
                    item.style.display = diffMonths <= 6 ? '' : 'none';
                }
            });
        }
    </script>
@endpush
