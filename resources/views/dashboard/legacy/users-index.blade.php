<section class="legacy-dashboard">
    <div class="content-header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
            <div>
                <h3 class="page-title mb-1">Gestión de Usuarios</h3>
                <p class="text-muted mb-0">Periodo activo · {{ $range['start'] }} – {{ $range['end'] }}</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="mdi mdi-home-outline"></i></a></li>
                    <li class="breadcrumb-item">Usuarios</li>
                    <li class="breadcrumb-item active" aria-current="page">Lista de usuarios</li>
                </ol>
            </nav>
        </div>
    </div>

    @php
        $cards = [
            [
                'label' => 'Pacientes',
                'value' => number_format($counts['patients'] ?? 0),
                'icon' => 'fa-user-injured',
            ],
            [
                'label' => 'Protocolos',
                'value' => number_format($counts['protocols'] ?? 0),
                'icon' => 'fa-notes-medical',
            ],
            [
                'label' => 'Usuarios',
                'value' => number_format($counts['users'] ?? 0),
                'icon' => 'fa-user-friends',
            ],
        ];
    @endphp

    <div class="row g-3 legacy-kpi-grid">
        @foreach($cards as $card)
            <div class="col">
                <div class="legacy-kpi-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="legacy-kpi-label mb-0">{{ $card['label'] }}</p>
                        <span class="badge bg-white text-primary shadow-sm"><i class="fa {{ $card['icon'] }}"></i></span>
                    </div>
                    <p class="legacy-kpi-value mb-0">{{ $card['value'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="box shadow-sm rounded">
                <div class="box-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div>
                        <h4 class="box-title mb-1">📋 <strong>Listado de Usuarios</strong></h4>
                        <p class="subtitle mb-0">Administra los usuarios registrados en el sistema.</p>
                    </div>
                    <button type="button" class="btn btn-primary mt-3 mt-md-0" disabled>
                        <i class="mdi mdi-account-plus"></i> Agregar usuario
                    </button>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm align-middle">
                            <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Usuario</th>
                                <th scope="col">Email</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Especialidad</th>
                                <th scope="col">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $user)
                                <tr data-user-id="{{ $user['id'] }}">
                                    <td>{{ $user['id'] }}</td>
                                    <td>{{ $user['username'] }}</td>
                                    <td>{{ $user['email'] ?? '—' }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{{ $user['nombre'] }}</span>
                                            <span class="legacy-badge {{ $user['is_approved'] ? 'success' : 'warning' }}">
                                                <i class="fa {{ $user['is_approved'] ? 'fa-check-circle' : 'fa-hourglass-half' }}"></i>
                                                {{ $user['is_approved'] ? 'Aprobado' : 'Pendiente' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>{{ $user['especialidad'] ?? '—' }}</td>
                                    <td>
                                        <button type="button"
                                                class="btn btn-outline-secondary btn-sm btn-ver-perfil"
                                                data-profile='@json($user)'>
                                            <i class="fas fa-id-badge"></i> Perfil
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No hay usuarios registrados.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.legacy.user-profile')
</section>
