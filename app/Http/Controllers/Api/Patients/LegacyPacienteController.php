<?php

namespace App\Http\Controllers\Api\Patients;

use App\Http\Controllers\Controller;
use App\Services\Pacientes\PacienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LegacyPacienteController extends Controller
{
    public function __construct(private readonly PacienteService $service)
    {
    }

    public function datatable(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'draw' => ['nullable', 'integer'],
            'start' => ['nullable', 'integer'],
            'length' => ['nullable', 'integer', 'max:200'],
            'search.value' => ['nullable', 'string'],
            'order.0.column' => ['nullable', 'integer'],
            'order.0.dir' => ['nullable', 'in:asc,desc'],
        ]);

        $payload = $this->buildDatatablePayload($validated);

        return response()->json($payload);
    }

    public function show(string $hcNumber): JsonResponse
    {
        $context = $this->service->detail($hcNumber);

        if (! $context) {
            return response()->json(['message' => 'Paciente no encontrado'], 404);
        }

        return response()->json($context);
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function buildDatatablePayload(array $validated): array
    {
        $columnMap = ['hc_number', 'ultima_fecha', 'full_name', 'afiliacion'];
        $orderIndex = data_get($validated, 'order.0.column', 0);
        $orderColumn = $columnMap[$orderIndex] ?? 'hc_number';

        $payload = $this->service->datatable(
            start: (int) ($validated['start'] ?? 0),
            length: (int) ($validated['length'] ?? 10),
            search: (string) data_get($validated, 'search.value', ''),
            orderColumn: $orderColumn,
            orderDir: data_get($validated, 'order.0.dir', 'asc')
        );

        $payload['draw'] = (int) ($validated['draw'] ?? 1);

        return $payload;
    }
}
