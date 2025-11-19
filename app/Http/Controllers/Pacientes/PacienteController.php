<?php

namespace App\Http\Controllers\Pacientes;

use App\Http\Controllers\Controller;
use App\Services\Pacientes\PacienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PacienteController extends Controller
{
    public function __construct(private readonly PacienteService $service)
    {
    }

    public function index(Request $request): View
    {
        return view('pacientes.index', [
            'showNotFoundAlert' => $request->boolean('not_found'),
        ]);
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

        return response()->json($payload);
    }

    public function show(string $hcNumber): View|RedirectResponse
    {
        $context = $this->service->detail($hcNumber);

        if (! $context) {
            return redirect()->route('pacientes.index', ['not_found' => 1]);
        }

        return view('pacientes.show', array_merge($context, [
            'hcNumber' => $hcNumber,
        ]));
    }
}
