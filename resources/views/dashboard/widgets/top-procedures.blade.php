<div class="box box-body shadow-sm h-100">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-1">Top procedimientos</h5>
            <p class="text-muted small mb-0">Ranking por membrete</p>
        </div>
    </div>
    @if(empty($topProcedures['labels']))
        <p class="text-muted mb-0">Sin datos en este rango.</p>
    @else
        <canvas id="topProceduresChart" height="200"></canvas>
    @endif
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Chart === 'undefined') {
                return;
            }
            const ctx = document.getElementById('topProceduresChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($topProcedures['labels']),
                    datasets: [{
                        label: 'Procedimientos',
                        data: @json($topProcedures['data']),
                        backgroundColor: '#0ea5e9',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        x: { ticks: { autoSkip: false }},
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                },
            });
        });
    </script>
@endpush
