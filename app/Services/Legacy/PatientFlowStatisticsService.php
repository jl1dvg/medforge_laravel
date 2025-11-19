<?php

namespace App\Services\Legacy;

use Illuminate\Database\ConnectionInterface;

class PatientFlowStatisticsService
{
    public function __construct(private readonly ConnectionInterface $connection)
    {
    }

    public function fetch(array $filters): array
    {
        $query = $this->connection->table('procedimiento_proyectado')
            ->select(['form_id', 'hc_number', 'doctor', 'sede_departamento', 'fecha', 'estado_agenda'])
            ->when($filters['fecha_inicio'] ?? null, fn ($q, $value) => $q->where('fecha', '>=', $value))
            ->when($filters['fecha_fin'] ?? null, fn ($q, $value) => $q->where('fecha', '<=', $value))
            ->when($filters['medico'] ?? null, fn ($q, $value) => $q->where('doctor', $value))
            ->when($filters['servicio'] ?? null, fn ($q, $value) => $q->where('sede_departamento', $value));

        $patients = $query->get();

        return $patients
            ->map(function ($row) {
                $timings = $this->calculateDurations((int) $row->form_id);

                return [
                    'form_id' => $row->form_id,
                    'hc_number' => $row->hc_number,
                    'doctor' => $row->doctor,
                    'servicio' => $row->sede_departamento,
                    'fecha' => $row->fecha,
                    'estado_agenda' => $row->estado_agenda,
                    'tiempos' => $timings,
                ];
            })
            ->all();
    }

    private function calculateDurations(int $formId): array
    {
        $appointment = $this->connection->table('procedimiento_proyectado')
            ->select(['fecha', 'hora'])
            ->where('form_id', $formId)
            ->first();

        $scheduledAt = null;
        if ($appointment && $appointment->fecha && $appointment->hora) {
            $scheduledAt = sprintf('%s %s', $appointment->fecha, $appointment->hora);
        }

        $states = $this->connection->table('procedimiento_proyectado_estado')
            ->select(['estado', 'fecha_hora_cambio'])
            ->where('form_id', $formId)
            ->orderBy('fecha_hora_cambio')
            ->get()
            ->keyBy('estado');

        return [
            'espera' => $this->minutesBetween($scheduledAt, $states['LLEGADO']->fecha_hora_cambio ?? null),
            'sala' => $this->minutesBetween($states['LLEGADO']->fecha_hora_cambio ?? null, $states['OPTOMETRIA']->fecha_hora_cambio ?? null),
            'optometria' => $this->minutesBetween(
                $states['OPTOMETRIA']->fecha_hora_cambio ?? null,
                $states['OPTOMETRIA_TERMINADO']->fecha_hora_cambio ?? $states['DILATAR']->fecha_hora_cambio ?? null
            ),
            'total' => $this->minutesBetween(
                $states['LLEGADO']->fecha_hora_cambio ?? null,
                $states['OPTOMETRIA_TERMINADO']->fecha_hora_cambio ?? $states['DILATAR']->fecha_hora_cambio ?? null
            ),
        ];
    }

    private function minutesBetween(?string $start, ?string $end): ?float
    {
        if (!$start || !$end) {
            return null;
        }

        $startTimestamp = strtotime($start);
        $endTimestamp = strtotime($end);

        if ($startTimestamp === false || $endTimestamp === false) {
            return null;
        }

        return round(($endTimestamp - $startTimestamp) / 60, 2);
    }
}
