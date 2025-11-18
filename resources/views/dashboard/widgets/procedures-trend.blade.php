<div class="box box-body shadow-sm h-100">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-1">Procedimientos por día</h5>
            <p class="text-muted small mb-0">Total en ventana: {{ number_format($trend['total']) }}</p>
        </div>
    </div>
    <canvas id="proceduresTrendChart" height="160"></canvas>
</div>

@push('scripts')
    @once
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @endonce
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('proceduresTrendChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($trend['labels']),
                    datasets: [{
                        label: 'Procedimientos',
                        data: @json($trend['data']),
                        fill: true,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.15)',
                        tension: 0.4,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                },
            });
        });
    </script>
@endpush
