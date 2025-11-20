<?php

namespace App\Services;

use App\Models\PatientIdentityCertification;
use App\Models\ProjectedProcedure;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AgendaService
{
    public function __construct(
        private readonly IdentityVerificationPolicy $policy,
        private readonly PatientContextService $patientContextService,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function resolveFilters(array $input): array
    {
        $today = now()->toDateString();
        $fechaInicio = $input['fecha_inicio'] ?? $today;
        $fechaFin = $input['fecha_fin'] ?? $fechaInicio;

        $inicio = Carbon::parse($fechaInicio)->toDateString();
        $fin = Carbon::parse($fechaFin)->toDateString();

        if ($fin < $inicio) {
            [$inicio, $fin] = [$fin, $inicio];
        }

        return [
            'fecha_inicio' => $inicio,
            'fecha_fin' => $fin,
            'doctor' => $this->trimValue($input['doctor'] ?? null),
            'estado' => $this->trimValue($input['estado'] ?? null),
            'sede' => $this->trimValue($input['sede'] ?? null),
            'solo_con_visita' => (bool) ($input['solo_con_visita'] ?? false),
        ];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getAgendaIndexData(array $filters): array
    {
        $query = ProjectedProcedure::query()
            ->with(['patient', 'visit'])
            ->leftJoin('visitas as filter_visitas', 'filter_visitas.id', '=', 'procedimiento_proyectado.visita_id')
            ->select('procedimiento_proyectado.*')
            ->whereBetween(DB::raw('COALESCE(DATE(procedimiento_proyectado.fecha), filter_visitas.fecha_visita)'), [
                $filters['fecha_inicio'],
                $filters['fecha_fin'],
            ])
            ->when($filters['solo_con_visita'], fn ($query) => $query->whereNotNull('visita_id'))
            ->when($filters['doctor'], fn ($query, $doctor) => $query->where('doctor', $doctor))
            ->when($filters['estado'], fn ($query, $estado) => $query->where('estado_agenda', $estado))
            ->when($filters['sede'], function ($query, $sede) {
                $query->where(function ($inner) use ($sede) {
                    $inner->where('id_sede', $sede)
                        ->orWhere('sede_departamento', $sede);
                });
            })
            ->orderByRaw('COALESCE(DATE(procedimiento_proyectado.fecha), filter_visitas.fecha_visita) ASC')
            ->orderByRaw('COALESCE(procedimiento_proyectado.hora, filter_visitas.hora_llegada, procedimiento_proyectado.fecha) ASC')
            ->orderBy('form_id');

        $procedures = $query->get();

        $states = ProjectedProcedure::query()
            ->whereNotNull('estado_agenda')
            ->where('estado_agenda', '!=', '')
            ->distinct()
            ->orderBy('estado_agenda')
            ->pluck('estado_agenda')
            ->all();

        $doctors = ProjectedProcedure::query()
            ->whereNotNull('doctor')
            ->where('doctor', '!=', '')
            ->distinct()
            ->orderBy('doctor')
            ->pluck('doctor')
            ->all();

        $sedes = ProjectedProcedure::query()
            ->selectRaw('DISTINCT NULLIF(id_sede, "") as id_sede, NULLIF(sede_departamento, "") as sede_departamento')
            ->get()
            ->filter(fn ($row) => $row->id_sede !== null || $row->sede_departamento !== null)
            ->map(fn ($row) => [
                'id_sede' => $row->id_sede,
                'sede_departamento' => $row->sede_departamento,
            ])
            ->values()
            ->all();

        return [
            'procedures' => $procedures,
            'availableStates' => $states,
            'availableDoctors' => $doctors,
            'availableLocations' => $sedes,
        ];
    }

    public function getVisitViewData(int $visitId): ?array
    {
        $visit = Visit::query()
            ->with(['patient', 'projectedProcedures.states'])
            ->find($visitId);

        if (! $visit) {
            return null;
        }

        $patientId = $visit->hc_number;
        $context = $patientId ? $this->patientContextService->buildContext($patientId) : ['coverageStatus' => 'N/A', 'timelineItems' => []];
        $coverage = $context['coverageStatus'] ?? 'N/A';

        $identityVerification = [
            'summary' => null,
            'requires_checkin' => true,
            'validity_days' => $this->policy->getValidityDays(),
        ];

        if ($patientId) {
            $summary = PatientIdentityCertification::query()
                ->with('patient')
                ->where('patient_id', $patientId)
                ->orderByDesc('updated_at')
                ->first();

            if ($summary) {
                $identityVerification['summary'] = $summary->toArray();
                $identityVerification['summary']['patient_full_name'] = $summary->patient?->full_name;
                $identityVerification['requires_checkin'] = $summary->status !== 'verified';
            }
        }

        $visitData = $this->transformVisit($visit, $context, $coverage);

        return [
            'visit' => $visitData,
            'identityVerification' => $identityVerification,
        ];
    }

    private function trimValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    /**
     * @return array<string, mixed>
     */
    private function transformVisit(Visit $visit, array $context, string $coverage): array
    {
        $patient = $visit->patient;
        $procedures = $visit->projectedProcedures instanceof Collection
            ? $visit->projectedProcedures
            : collect();

        $procedureData = $procedures
            ->sortBy(fn (ProjectedProcedure $procedure) => [
                $procedure->agenda_date,
                $procedure->agenda_time,
                $procedure->form_id,
            ])
            ->map(function (ProjectedProcedure $procedure) {
                return [
                    'id' => $procedure->id,
                    'form_id' => $procedure->form_id,
                    'procedimiento' => $procedure->procedimiento_proyectado,
                    'doctor' => $procedure->doctor,
                    'sede_departamento' => $procedure->sede_departamento,
                    'id_sede' => $procedure->id_sede,
                    'estado_agenda' => $procedure->estado_agenda,
                    'hora_agenda' => $procedure->agenda_time,
                    'historial_estados' => $procedure->states
                        ->map(fn ($state) => [
                            'estado' => $state->estado,
                            'fecha_hora_cambio' => optional($state->fecha_hora_cambio)->toDateTimeString(),
                        ])->toArray(),
                ];
            })
            ->values()
            ->toArray();

        return [
            'id' => $visit->id,
            'hc_number' => $visit->hc_number,
            'fecha_visita' => optional($visit->fecha_visita)->toDateTimeString(),
            'hora_llegada' => optional($visit->hora_llegada)->toDateTimeString(),
            'usuario_registro' => $visit->usuario_registro,
            'afiliacion' => $patient?->afiliacion,
            'celular' => $patient?->celular,
            'paciente' => $patient?->full_name,
            'procedimientos' => $procedureData,
            'paciente_contexto' => $context,
            'estado_cobertura' => $coverage,
        ];
    }
}
