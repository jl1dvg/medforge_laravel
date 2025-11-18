@php
    $cards = [
        [
            'label' => 'Pacientes activos',
            'value' => number_format($counts['patients'] ?? 0),
            'icon' => 'fa-solid fa-user-group text-primary',
        ],
        [
            'label' => 'Protocolos registrados',
            'value' => number_format($counts['protocols'] ?? 0),
            'icon' => 'fa-solid fa-notes-medical text-success',
        ],
        [
            'label' => 'Usuarios',
            'value' => number_format($counts['users'] ?? 0),
            'icon' => 'fa-solid fa-user-shield text-warning',
        ],
    ];
@endphp

<div class="row g-3">
    @foreach($cards as $card)
        <div class="col-md-4">
            <div class="box box-body h-100 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted text-uppercase fs-12 mb-1">{{ $card['label'] }}</p>
                        <h3 class="mb-0 fw-bold">{{ $card['value'] }}</h3>
                    </div>
                    <div class="display-6">
                        <i class="{{ $card['icon'] }}"></i>
                    </div>
                </div>
                <p class="text-muted small mb-0 mt-2">
                    Ventana: {{ $range['start'] }} — {{ $range['end'] }}
                </p>
            </div>
        </div>
    @endforeach
</div>
