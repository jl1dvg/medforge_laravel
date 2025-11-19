@extends('layouts.app')

@section('title', 'Encuentro #' . ($visit['id'] ?? ''))

@php
    use App\Support\AgendaViewHelper;

    $procedimientos = $visit['procedimientos'] ?? [];
    $identityVerification = $identityVerification ?? ['summary' => null, 'requires_checkin' => true, 'validity_days' => null];
    $verificationSummary = $identityVerification['summary'] ?? null;
    $requiresBiometricCheckin = (bool) ($identityVerification['requires_checkin'] ?? true);
    $verificationStatus = strtolower((string) ($verificationSummary['status'] ?? 'sin_registro'));
    $verificationBadge = match ($verificationStatus) {
        'verified' => 'badge bg-success',
        'expired', 'revoked' => 'badge bg-danger',
        'pending' => 'badge bg-warning text-dark',
        default => 'badge bg-secondary',
    };
    $verificationLabel = match ($verificationStatus) {
        'verified' => 'Verificada',
        'expired' => 'Vencida',
        'revoked' => 'Revocada',
        'pending' => 'Pendiente',
        default => 'Sin certificación',
    };
    $lastVerificationAt = $verificationSummary['last_verification_at'] ?? null;
    $lastVerificationResult = $verificationSummary['last_verification_result'] ?? null;
    $expiredAt = $verificationSummary['expired_at'] ?? null;
    $fechaVisita = $visit['fecha_visita'] ? \Carbon\Carbon::parse($visit['fecha_visita'])->format('d/m/Y') : '—';
    $horaLlegada = $visit['hora_llegada'] ? \Carbon\Carbon::parse($visit['hora_llegada'])->format('H:i') : '—';
    $nombrePaciente = $visit['paciente'] ?? 'Paciente sin nombre';
    $hcNumber = $visit['hc_number'] ?? null;
    $timelineResumen = collect($visit['paciente_contexto']['timelineItems'] ?? [])->take(5);
    $estadoCobertura = $visit['paciente_contexto']['coverageStatus'] ?? ($visit['estado_cobertura'] ?? 'N/A');
@endphp

@section('content')
<section class="content-header">
    <div class="d-flex align-items-center">
        <div class="me-auto">
            <h3 class="page-title">Encuentro #{{ $visit['id'] }}</h3>
            <div class="d-inline-block align-items-center">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('agenda.index') }}">Agenda</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Encuentro</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="ms-auto">
            @if ($hcNumber)
                <a class="btn btn-outline-primary" href="{{ route('pacientes.show', $hcNumber) }}">
                    <i class="mdi mdi-account"></i> Ver ficha del paciente
                </a>
            @endif
        </div>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-lg-5">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Datos del encuentro</h4>
                </div>
                <div class="box-body">
                    @php
                        $verificationUrl = $hcNumber ? route('pacientes.show', $hcNumber) . '#certificaciones' : null;
                        $validityDays = $identityVerification['validity_days'] ?? null;
                    @endphp
                    @if (! $hcNumber)
                        <div class="alert alert-info">
                            <strong>Historia clínica no asignada.</strong> Vincule el encuentro con un paciente para habilitar la certificación biométrica.
                        </div>
                    @elseif ($verificationSummary === null)
                        <div class="alert alert-danger d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <strong>Certificación biométrica pendiente.</strong>
                                Debe registrar firma y rostro del paciente antes de continuar con la atención.
                            </div>
                            @if ($verificationUrl)
                                <a class="btn btn-sm btn-primary" href="{{ $verificationUrl }}">
                                    <i class="mdi mdi-face-recognition"></i> Abrir módulo de certificación
                                </a>
                            @endif
                        </div>
                    @else
                        @if ($requiresBiometricCheckin)
                            <div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    <strong>Revisar certificación biométrica.</strong>
                                    <span class="{{ $verificationBadge }} ms-2">Estado: {{ $verificationLabel }}</span>
                                    @if ($expiredAt)
                                        <div class="small text-muted">Vencida desde {{ \Carbon\Carbon::parse($expiredAt)->format('d/m/Y H:i') }}.</div>
                                    @endif
                                    @if ($lastVerificationAt)
                                        <div class="small text-muted">Última verificación: {{ \Carbon\Carbon::parse($lastVerificationAt)->format('d/m/Y H:i') }} · Resultado: {{ $lastVerificationResult ?? 'N/A' }}</div>
                                    @endif
                                    @if ($validityDays)
                                        <div class="small text-muted">Vigencia configurada: {{ (int) $validityDays }} días.</div>
                                    @endif
                                </div>
                                @if ($verificationUrl)
                                    <a class="btn btn-sm btn-outline-primary" href="{{ $verificationUrl }}">
                                        <i class="mdi mdi-face-recognition"></i> Actualizar datos biométricos
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-success d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    <strong>Certificación biométrica vigente.</strong>
                                    <span class="{{ $verificationBadge }} ms-2">Estado: {{ $verificationLabel }}</span>
                                    @if ($lastVerificationAt)
                                        <div class="small text-muted">Última verificación: {{ \Carbon\Carbon::parse($lastVerificationAt)->format('d/m/Y H:i') }} · Resultado: {{ $lastVerificationResult ?? 'N/A' }}</div>
                                    @endif
                                </div>
                                @if ($verificationUrl)
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ $verificationUrl }}">
                                        <i class="mdi mdi-file-document"></i> Ver detalle de certificación
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endif

                    <dl class="row mb-0">
                        <dt class="col-sm-5">Paciente</dt>
                        <dd class="col-sm-7 fw-600">{{ $nombrePaciente }}</dd>

                        <dt class="col-sm-5">Historia clínica</dt>
                        <dd class="col-sm-7">{{ $hcNumber ?? '—' }}</dd>

                        <dt class="col-sm-5">Afiliación</dt>
                        <dd class="col-sm-7">{{ $visit['afiliacion'] ?? '—' }}</dd>

                        <dt class="col-sm-5">Fecha de visita</dt>
                        <dd class="col-sm-7">{{ $fechaVisita }}</dd>

                        <dt class="col-sm-5">Hora de llegada</dt>
                        <dd class="col-sm-7">{{ $horaLlegada }}</dd>

                        <dt class="col-sm-5">Usuario que registró</dt>
                        <dd class="col-sm-7">{{ $visit['usuario_registro'] ?? '—' }}</dd>

                        <dt class="col-sm-5">Contacto</dt>
                        <dd class="col-sm-7">{{ $visit['celular'] ?? '—' }}</dd>

                        <dt class="col-sm-5">Estado de cobertura</dt>
                        <dd class="col-sm-7">
                            <span class="{{ AgendaViewHelper::coverageBadge($estadoCobertura) }}">{{ $estadoCobertura }}</span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="box">
                <div class="box-header with-border d-flex align-items-center justify-content-between">
                    <h4 class="box-title mb-0">Procedimientos asociados</h4>
                    <span class="badge bg-primary">{{ count($procedimientos) }} procedimientos</span>
                </div>
                <div class="box-body p-0">
                    @if ($procedimientos)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="bg-primary-light">
                                <tr>
                                    <th>Form ID</th>
                                    <th>Procedimiento</th>
                                    <th>Doctor</th>
                                    <th>Horario</th>
                                    <th>Estado actual</th>
                                    <th>Historial</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($procedimientos as $procedimiento)
                                    @php
                                        $estado = $procedimiento['estado_agenda'] ?? 'Sin estado';
                                        $hora = $procedimiento['hora_agenda'] ?? '—';
                                        $historial = $procedimiento['historial_estados'] ?? [];
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="badge bg-info-light text-primary fw-600">{{ $procedimiento['form_id'] }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-600 text-dark">{{ $procedimiento['procedimiento'] ?? '—' }}</div>
                                            <div class="text-muted small">{{ $procedimiento['sede_departamento'] ?? ($procedimiento['id_sede'] ?? '—') }}</div>
                                        </td>
                                        <td>{{ $procedimiento['doctor'] ?? '—' }}</td>
                                        <td>{{ $hora }}</td>
                                        <td>
                                            <span class="{{ AgendaViewHelper::badgeClass($estado) }}">{{ $estado }}</span>
                                        </td>
                                        <td style="min-width: 220px;">
                                            @if ($historial)
                                                <ul class="list-unstyled mb-0 small">
                                                    @foreach ($historial as $evento)
                                                        <li>
                                                            <span class="text-muted">{{ $evento['fecha_hora_cambio'] ? \Carbon\Carbon::parse($evento['fecha_hora_cambio'])->format('d/m H:i') : '—' }}</span>
                                                            <span class="ms-1 fw-500">{{ $evento['estado'] ?? '—' }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted">Sin registros</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            No se registraron procedimientos para este encuentro.
                        </div>
                    @endif
                </div>
            </div>
            @if ($timelineResumen->isNotEmpty())
                <div class="box mt-4">
                    <div class="box-header with-border d-flex align-items-center justify-content-between">
                        <h4 class="box-title mb-0">Últimos movimientos del paciente</h4>
                        <span class="badge bg-secondary-light text-secondary">{{ $timelineResumen->count() }} registros recientes</span>
                    </div>
                    <div class="box-body">
                        <ul class="list-unstyled mb-0">
                            @foreach ($timelineResumen as $item)
                                @php
                                    $fechaItem = isset($item['fecha']) && $item['fecha'] !== ''
                                        ? \Carbon\Carbon::parse($item['fecha'])->format('d/m/Y H:i')
                                        : 'Fecha no disponible';
                                    $tipoItem = strtoupper((string) ($item['tipo'] ?? $item['origen'] ?? ''));
                                    $nombreItem = $item['nombre'] ?? $item['procedimiento'] ?? 'Movimiento';
                                @endphp
                                <li class="mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-600 text-dark">{{ $nombreItem }}</div>
                                            <div class="text-muted small">{{ $fechaItem }}</div>
                                        </div>
                                        @if ($tipoItem !== '')
                                            <span class="badge bg-primary-light text-primary ms-3">{{ $tipoItem }}</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
