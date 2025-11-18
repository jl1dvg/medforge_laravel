<div class="box box-body shadow-sm h-100">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-1">Cirugías recientes</h5>
            <p class="text-muted small mb-0">Últimos procedimientos del rango</p>
        </div>
    </div>
    @if($recentSurgeries->isEmpty())
        <p class="text-muted mb-0">No se registraron cirugías en este periodo.</p>
    @else
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Paciente</th>
                    <th>Procedimiento</th>
                    <th>Doctor</th>
                </tr>
                </thead>
                <tbody>
                @foreach($recentSurgeries as $surgery)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($surgery['fecha_inicio'])->format('d/m/Y') }}</td>
                        <td>
                            <span class="fw-semibold">{{ $surgery['patient_name'] }}</span><br>
                            <span class="text-muted small">{{ $surgery['hc_number'] }}</span>
                        </td>
                        <td>{{ $surgery['membrete'] ?? 'Sin membrete' }}</td>
                        <td>{{ $surgery['doctor_name'] ?? 'Sin asignar' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
