<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LegacyDashboardService
{
    /**
     * Resolve the date range coming from the request.
     */
    public function resolveDateRange(?string $start, ?string $end): array
    {
        $endDate = $this->parseDate($end) ?? Carbon::today();
        $startDate = $this->parseDate($start) ?? $endDate->copy()->subDays(29);

        if ($startDate->greaterThan($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->subDays(29), $startDate];
        }

        return [
            'start' => $startDate->startOfDay(),
            'end' => $endDate->endOfDay(),
            'label' => $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'),
        ];
    }

    /**
     * Assemble all dashboard data in one call.
     */
    public function build(Carbon $start, Carbon $end): array
    {
        $proceduresByDay = $this->proceduresByDay($start, $end);
        $topProcedures = $this->topProcedures($start, $end);

        return [
            'date_range' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
                'label' => $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y'),
            ],
            'procedimientos_dia' => $proceduresByDay,
            'top_procedimientos' => $topProcedures,
            'cirugias_recientes' => $this->recentSurgeries($start, $end),
            'plantillas' => $this->recentTemplates(),
            'diagnosticos_frecuentes' => $this->commonDiagnoses(),
            'solicitudes_quirurgicas' => $this->latestRequests(),
            'doctores_top' => $this->topDoctors(),
            'estadisticas_afiliacion' => $this->affiliationStats(),
            'revision_estados' => $this->revisionStatus(),
            'solicitudes_funnel' => $this->requestsFunnel($start, $end),
            'crm_backlog' => $this->crmBacklog($start, $end),
            'total_cirugias_periodo' => $this->totalSurgeries($start, $end),
            'total_protocols' => $this->totalProtocols(),
            'total_patients' => $this->totalPatients(),
            'total_users' => $this->totalUsers(),
            'kpi_cards' => $this->kpiCards($start, $end),
            'ai_summary' => $this->aiSummary(),
        ];
    }

    private function parseDate(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y'] as $format) {
            $parsed = Carbon::createFromFormat($format, $value);
            if ($parsed !== false) {
                return $parsed;
            }
        }

        return null;
    }

    private function totalPatients(): int
    {
        return (int) DB::table('patient_data')->count();
    }

    private function totalUsers(): int
    {
        return (int) DB::table('users')->count();
    }

    private function totalProtocols(): int
    {
        return (int) DB::table('protocolo_data')->count();
    }

    private function totalSurgeries(Carbon $start, Carbon $end): int
    {
        return (int) DB::table('protocolo_data')
            ->whereBetween('fecha_inicio', [$start, $end])
            ->count();
    }

    private function proceduresByDay(Carbon $start, Carbon $end): array
    {
        $rows = DB::table('protocolo_data')
            ->selectRaw('DATE(fecha_inicio) as fecha, COUNT(*) as total')
            ->whereBetween('fecha_inicio', [$start, $end])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $fechas = $rows->pluck('fecha')->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))->all();
        $totales = $rows->pluck('total')->map(fn ($value) => (int) $value)->all();

        return [
            'fechas' => $fechas ?: ['No data'],
            'totales' => $totales ?: [0],
        ];
    }

    private function topProcedures(Carbon $start, Carbon $end): array
    {
        $rows = DB::table('protocolo_data')
            ->selectRaw('procedimiento_id, COUNT(*) as total')
            ->whereNotNull('procedimiento_id')
            ->where('procedimiento_id', '!=', '')
            ->whereBetween('fecha_inicio', [$start, $end])
            ->groupBy('procedimiento_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'membretes' => $rows->pluck('procedimiento_id')->all() ?: ['No data'],
            'totales' => $rows->pluck('total')->map(fn ($value) => (int) $value)->all() ?: [0],
        ];
    }

    private function recentSurgeries(Carbon $start, Carbon $end, int $limit = 8): array
    {
        $rows = DB::table('protocolo_data as pr')
            ->join('patient_data as p', 'p.hc_number', '=', 'pr.hc_number')
            ->select([
                'p.hc_number',
                'p.fname',
                'p.lname',
                'p.lname2',
                'p.fecha_nacimiento',
                'p.ciudad',
                'p.afiliacion',
                'pr.fecha_inicio',
                'pr.id',
                'pr.membrete',
                'pr.form_id',
            ])
            ->where('p.afiliacion', '!=', 'ALQUILER')
            ->whereBetween('pr.fecha_inicio', [$start, $end])
            ->orderByDesc('pr.fecha_inicio')
            ->orderByDesc('pr.id')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        return [
            'total' => count($rows),
            'data' => $rows,
        ];
    }

    private function recentTemplates(int $limit = 20): array
    {
        return DB::table('procedimientos')
            ->select([
                'id',
                'membrete',
                'cirugia',
                DB::raw('COALESCE(fecha_actualizacion, fecha_creacion) AS fecha'),
                DB::raw("CASE WHEN fecha_actualizacion IS NOT NULL THEN 'Modificado' ELSE 'Creado' END AS tipo"),
            ])
            ->orderByDesc('fecha')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function latestRequests(int $limit = 5): array
    {
        $records = DB::table('solicitud_procedimiento as sp')
            ->join('patient_data as p', 'p.hc_number', '=', 'sp.hc_number')
            ->select(['sp.id', 'sp.fecha', 'sp.procedimiento', 'p.fname', 'p.lname', 'p.hc_number'])
            ->whereNotNull('sp.procedimiento')
            ->where('sp.procedimiento', '!=', '')
            ->where('sp.procedimiento', '!=', 'SELECCIONE')
            ->orderByDesc('sp.fecha')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        $total = DB::table('solicitud_procedimiento')
            ->whereNotNull('procedimiento')
            ->where('procedimiento', '!=', '')
            ->where('procedimiento', '!=', 'SELECCIONE')
            ->count();

        return [
            'solicitudes' => $records,
            'total' => (int) $total,
        ];
    }

    private function topDoctors(): array
    {
        $cutoff = Carbon::now()->subMonths(3);

        return DB::table('protocolo_data')
            ->selectRaw('cirujano_1, COUNT(*) as total')
            ->whereNotNull('cirujano_1')
            ->where('cirujano_1', '!=', '')
            ->where('fecha_inicio', '>=', $cutoff)
            ->groupBy('cirujano_1')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($row) => ['cirujano_1' => $row->cirujano_1, 'total' => (int) $row->total])
            ->all();
    }

    private function affiliationStats(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end = (clone $start)->addMonth();

        $rows = DB::table('protocolo_data as pr')
            ->join('patient_data as p', 'p.hc_number', '=', 'pr.hc_number')
            ->selectRaw('p.afiliacion, COUNT(*) as total_procedimientos')
            ->whereBetween('pr.fecha_inicio', [$start, $end])
            ->groupBy('p.afiliacion')
            ->get();

        return [
            'afiliaciones' => $rows->pluck('afiliacion')->all() ?: ['No data'],
            'totales' => $rows->pluck('total_procedimientos')->map(fn ($value) => (int) $value)->all() ?: [0],
        ];
    }

    private function revisionStatus(): array
    {
        $rows = DB::table('protocolo_data as pr')
            ->leftJoin('procedimiento_proyectado as pp', function ($join) {
                $join->on('pp.form_id', '=', 'pr.form_id')
                    ->on('pp.hc_number', '=', 'pr.hc_number');
            })
            ->select([
                'pr.status',
                'pr.membrete',
                'pr.dieresis',
                'pr.exposicion',
                'pr.hallazgo',
                'pr.operatorio',
                'pr.complicaciones_operatorio',
                'pr.datos_cirugia',
                'pr.procedimientos',
                'pr.lateralidad',
                'pr.tipo_anestesia',
                'pr.diagnosticos',
                'pp.procedimiento_proyectado',
                'pr.cirujano_1',
                'pr.instrumentista',
                'pr.cirujano_2',
                'pr.circulante',
                'pr.primer_ayudante',
                'pr.anestesiologo',
                'pr.segundo_ayudante',
                'pr.ayudante_anestesia',
                'pr.tercer_ayudante',
            ])
            ->orderByDesc('pr.fecha_inicio')
            ->orderByDesc('pr.id')
            ->get();

        $invalidValues = ['CENTER', 'undefined'];
        $incompletos = 0;
        $revisados = 0;
        $noRevisados = 0;

        foreach ($rows as $row) {
            $status = (int) ($row->status ?? 0);
            if ($status === 1) {
                $revisados++;
                continue;
            }

            $required = [
                $row->membrete,
                $row->dieresis,
                $row->exposicion,
                $row->hallazgo,
                $row->operatorio,
                $row->complicaciones_operatorio,
                $row->datos_cirugia,
                $row->procedimientos,
                $row->lateralidad,
                $row->tipo_anestesia,
                $row->diagnosticos,
                $row->procedimiento_proyectado,
            ];

            $staff = [
                $row->cirujano_1,
                $row->instrumentista,
                $row->cirujano_2,
                $row->circulante,
                $row->primer_ayudante,
                $row->anestesiologo,
                $row->segundo_ayudante,
                $row->ayudante_anestesia,
                $row->tercer_ayudante,
            ];

            $invalid = false;
            foreach ($required as $field) {
                foreach ($invalidValues as $value) {
                    if (!empty($field) && stripos((string) $field, $value) !== false) {
                        $invalid = true;
                        break 2;
                    }
                }
            }

            $staffCount = 0;
            if (!empty($row->cirujano_1)) {
                foreach ($staff as $field) {
                    foreach ($invalidValues as $value) {
                        if (!empty($field) && stripos((string) $field, $value) !== false) {
                            $invalid = true;
                            break 2;
                        }
                    }
                    if (!empty($field)) {
                        $staffCount++;
                    }
                }
            } else {
                $invalid = true;
            }

            if (!$invalid && $staffCount >= 5) {
                $noRevisados++;
            } else {
                $incompletos++;
            }
        }

        return [
            'incompletos' => $incompletos,
            'revisados' => $revisados,
            'no_revisados' => $noRevisados,
        ];
    }

    private function commonDiagnoses(): array
    {
        $rows = DB::table('consulta_data')
            ->select(['hc_number', 'diagnosticos'])
            ->whereNotNull('diagnosticos')
            ->where('diagnosticos', '!=', '')
            ->get();

        $conteo = [];

        foreach ($rows as $row) {
            $diagnosticos = json_decode($row->diagnosticos, true);
            if (!is_array($diagnosticos)) {
                continue;
            }

            foreach ($diagnosticos as $dx) {
                $id = isset($dx['idDiagnostico'])
                    ? strtoupper(str_replace('.', '', $dx['idDiagnostico']))
                    : 'SINID';

                $desc = is_array($dx) && array_key_exists('descripcion', $dx)
                    ? $dx['descripcion']
                    : 'Sin descripción';

                if (stripos($id, 'Z') === 0) {
                    continue;
                }

                $key = in_array($id, ['H25', 'H251'], true)
                    ? 'H25 | Catarata senil'
                    : $id . ' | ' . $desc;

                $conteo[$key][$row->hc_number] = true;
            }
        }

        $prevalencias = [];
        foreach ($conteo as $key => $pacientes) {
            $prevalencias[$key] = count($pacientes);
        }

        arsort($prevalencias);

        return array_slice($prevalencias, 0, 9, true);
    }

    private function requestsFunnel(Carbon $start, Carbon $end): array
    {
        $rows = DB::table('solicitud_procedimiento')
            ->select(['estado', 'prioridad', 'turno', 'id'])
            ->whereNotNull('procedimiento')
            ->where('procedimiento', '!=', '')
            ->where('procedimiento', '!=', 'SELECCIONE')
            ->whereBetween(DB::raw('COALESCE(created_at, fecha)'), [$start, $end])
            ->get();

        $etapas = [
            'recibido' => 0,
            'llamado' => 0,
            'en-atencion' => 0,
            'revision-codigos' => 0,
            'docs-completos' => 0,
            'aprobacion-anestesia' => 0,
            'listo-para-agenda' => 0,
            'agendado' => 0,
        ];

        $prioridades = [
            'urgente' => 0,
            'alta' => 0,
            'media' => 0,
            'baja' => 0,
        ];

        foreach ($rows as $row) {
            $estado = $row->estado ? strtolower($row->estado) : 'recibido';
            if (array_key_exists($estado, $etapas)) {
                $etapas[$estado]++;
            }

            $prioridad = $row->prioridad ? strtolower($row->prioridad) : null;
            if ($prioridad && array_key_exists($prioridad, $prioridades)) {
                $prioridades[$prioridad]++;
            }
        }

        $agendadas = $rows->where('turno', '!=', null)->count();
        $registradas = $rows->count();
        $conversion = $registradas > 0 ? round(($agendadas / $registradas) * 100, 1) : 0.0;

        $conCirugia = DB::table('solicitud_procedimiento as sp')
            ->join('protocolo_data as pr', function ($join) {
                $join->on('pr.form_id', '=', 'sp.form_id')
                    ->on('pr.hc_number', '=', 'sp.hc_number');
            })
            ->whereNotNull('sp.procedimiento')
            ->where('sp.procedimiento', '!=', '')
            ->where('sp.procedimiento', '!=', 'SELECCIONE')
            ->whereBetween(DB::raw('COALESCE(sp.created_at, sp.fecha)'), [$start, $end])
            ->whereBetween('pr.fecha_inicio', [$start, $end])
            ->count();

        return [
            'etapas' => $etapas,
            'prioridades' => $prioridades,
            'totales' => [
                'registradas' => $registradas,
                'agendadas' => $agendadas,
                'con_cirugia' => $conCirugia,
            ],
            'totales_porcentajes' => [
                'conversion_agendada' => $conversion,
            ],
        ];
    }

    private function crmBacklog(Carbon $start, Carbon $end): array
    {
        $row = DB::table('solicitud_crm_tareas as t')
            ->join('solicitud_procedimiento as sp', 'sp.id', '=', 't.solicitud_id')
            ->selectRaw(
                "SUM(CASE WHEN t.estado IN ('pendiente','en_progreso') THEN 1 ELSE 0 END) AS pendientes, " .
                "SUM(CASE WHEN t.estado = 'completado' THEN 1 ELSE 0 END) AS completadas, " .
                "SUM(CASE WHEN t.estado IN ('pendiente','en_progreso') AND t.due_date < CURDATE() THEN 1 ELSE 0 END) AS vencidas, " .
                "SUM(CASE WHEN t.estado IN ('pendiente','en_progreso') AND DATE(t.due_date) = CURDATE() THEN 1 ELSE 0 END) AS vencen_hoy"
            )
            ->whereBetween(DB::raw('COALESCE(sp.created_at, sp.fecha)'), [$start, $end])
            ->first();

        $pendientes = (int) ($row->pendientes ?? 0);
        $completadas = (int) ($row->completadas ?? 0);
        $vencidas = (int) ($row->vencidas ?? 0);
        $vencenHoy = (int) ($row->vencen_hoy ?? 0);
        $total = $pendientes + $completadas;
        $avance = $total > 0 ? round(($completadas / $total) * 100, 1) : 0.0;

        return [
            'pendientes' => $pendientes,
            'completadas' => $completadas,
            'vencidas' => $vencidas,
            'vencen_hoy' => $vencenHoy,
            'avance' => $avance,
        ];
    }

    private function aiSummary(): array
    {
        return [
            'provider' => '',
            'features' => [
                'consultas_enfermedad' => false,
                'consultas_plan' => false,
            ],
            'provider_configured' => false,
        ];
    }

    private function kpiCards(Carbon $start, Carbon $end): array
    {
        $requests = $this->requestsFunnel($start, $end);
        $crm = $this->crmBacklog($start, $end);
        $revision = $this->revisionStatus();

        return [
            [
                'title' => 'Solicitudes registradas',
                'value' => $requests['totales']['registradas'] ?? 0,
                'description' => 'En el periodo seleccionado',
                'tag' => ($requests['totales_porcentajes']['conversion_agendada'] ?? 0) . '% agenda',
                'icon' => null,
            ],
            [
                'title' => 'Tareas CRM',
                'value' => $crm['pendientes'] + $crm['completadas'],
                'description' => 'Avance ' . ($crm['avance'] ?? 0) . '%',
                'tag' => $crm['vencidas'] . ' vencidas',
                'icon' => null,
            ],
            [
                'title' => 'Protocolos listos',
                'value' => $revision['no_revisados'] ?? 0,
                'description' => 'Pendientes de revisión',
                'tag' => ($revision['revisados'] ?? 0) . ' revisados',
                'icon' => null,
            ],
        ];
    }
}

