<?php

namespace App\Services;

use App\Models\PrefacturaPaciente;
use App\Models\SolicitudProcedimiento;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PatientContextService
{
    public function getCoverageStatus(string $hcNumber): string
    {
        try {
            $prefactura = PrefacturaPaciente::query()
                ->where('hc_number', $hcNumber)
                ->whereNotNull('cod_derivacion')
                ->where('cod_derivacion', '!=', '')
                ->orderByDesc('fecha_vigencia')
                ->first();
        } catch (QueryException) {
            return 'N/A';
        }

        if (! $prefactura || ! $prefactura->fecha_vigencia) {
            return 'N/A';
        }

        return $prefactura->fecha_vigencia->isFuture() ? 'Con Cobertura' : 'Sin Cobertura';
    }

    /**
     * @return array{coverageStatus:string,timelineItems:array<int, array<string, mixed>>}
     */
    public function buildContext(string $hcNumber, int $limit = 10): array
    {
        $coverage = $this->getCoverageStatus($hcNumber);
        $timeline = $this->buildTimeline($hcNumber, $limit);

        return [
            'coverageStatus' => $coverage,
            'timelineItems' => $timeline,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function buildTimeline(string $hcNumber, int $limit): array
    {
        try {
            $solicitudes = SolicitudProcedimiento::query()
                ->where('hc_number', $hcNumber)
                ->whereNotNull('procedimiento')
                ->where('procedimiento', '!=', '')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get()
                ->map(function (SolicitudProcedimiento $solicitud) {
                    return [
                        'nombre' => $solicitud->procedimiento,
                        'fecha' => optional($solicitud->created_at)->toDateTimeString(),
                        'tipo' => strtolower($solicitud->tipo ?? 'solicitud'),
                        'origen' => 'Solicitud',
                    ];
                });
        } catch (QueryException) {
            $solicitudes = Collection::make();
        }

        try {
            $prefacturas = PrefacturaPaciente::query()
                ->where('hc_number', $hcNumber)
                ->orderByDesc('fecha_creacion')
                ->limit($limit)
                ->get()
                ->map(function (PrefacturaPaciente $prefactura) {
                    return [
                        'nombre' => 'Prefactura',
                        'fecha' => optional($prefactura->fecha_creacion ?? $prefactura->fecha_registro)->toDateTimeString(),
                        'tipo' => 'prefactura',
                        'origen' => 'Prefactura',
                    ];
                });
        } catch (QueryException) {
            $prefacturas = Collection::make();
        }

        return $solicitudes
            ->merge($prefacturas)
            ->sortByDesc(function (array $item): int {
                $timestamp = $item['fecha'] ? Carbon::parse($item['fecha'])->getTimestamp() : 0;

                return $timestamp;
            })
            ->values()
            ->toArray();
    }
}
