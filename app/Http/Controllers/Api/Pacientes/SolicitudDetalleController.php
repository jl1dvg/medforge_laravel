<?php

namespace App\Http\Controllers\Api\Pacientes;

use App\Http\Controllers\Controller;
use App\Services\Pacientes\PacienteService;
use Illuminate\Http\JsonResponse;

class SolicitudDetalleController extends Controller
{
    public function __construct(private readonly PacienteService $service)
    {
    }

    public function show(string $hcNumber, string $formId): JsonResponse
    {
        $detalle = $this->service->solicitudDetalle($hcNumber, $formId);

        if (! $detalle) {
            return response()->json([
                'message' => 'Solicitud no encontrada.',
            ], 404);
        }

        return response()->json($detalle);
    }
}
