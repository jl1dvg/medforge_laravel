<?php

namespace App\Services\Pacientes;

use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PacienteService
{
    private ?bool $prefacturaTableExists = null;

    public function datatable(int $start, int $length, string $search, string $orderColumn, string $orderDir): array
    {
        $start = max($start, 0);
        $length = min(max($length, 1), 200);

        $baseQuery = DB::table('patient_data as p')
            ->leftJoinSub(
                DB::table('consulta_data')
                    ->selectRaw('hc_number, MAX(fecha) as ultima_fecha')
                    ->groupBy('hc_number'),
                'ultima',
                'ultima.hc_number',
                '=',
                'p.hc_number'
            )
            ->select([
                'p.hc_number',
                DB::raw("CONCAT(p.fname, ' ', COALESCE(p.mname,''), ' ', p.lname, ' ', COALESCE(p.lname2,'')) as full_name"),
                'p.afiliacion',
                'ultima.ultima_fecha',
            ]);

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('p.hc_number', 'like', "%{$search}%")
                    ->orWhere(DB::raw("CONCAT(p.fname,' ',COALESCE(p.mname,''),' ',p.lname,' ',COALESCE(p.lname2,''))"), 'like', "%{$search}%")
                    ->orWhere('p.afiliacion', 'like', "%{$search}%");
            });
        }

        $countFiltered = (clone $baseQuery)->count();
        $countTotal = DB::table('patient_data')->count();

        $columnsMap = [
            'hc_number' => 'p.hc_number',
            'ultima_fecha' => 'ultima.ultima_fecha',
            'full_name' => DB::raw("CONCAT(p.fname, ' ', COALESCE(p.mname,''), ' ', p.lname, ' ', COALESCE(p.lname2,''))"),
            'afiliacion' => 'p.afiliacion',
        ];

        $direction = strtolower($orderDir) === 'desc' ? 'desc' : 'asc';
        $orderBy = $columnsMap[$orderColumn] ?? $columnsMap['hc_number'];
        $baseQuery->orderBy($orderBy, $direction);

        $rows = $baseQuery->skip($start)->take($length)->get();

        $data = $rows->map(function ($row) {
            $ultimaFecha = $row->ultima_fecha ? date('d/m/Y', strtotime($row->ultima_fecha)) : '';
            $hc = $row->hc_number;
            $actions = $hc
                ? "<a href='" . route('pacientes.show', ['hcNumber' => $hc]) . "' class='btn btn-sm btn-primary'>Ver</a>"
                : '<span class="text-muted">—</span>';

            return [
                'hc_number' => $row->hc_number,
                'ultima_fecha' => $ultimaFecha,
                'full_name' => trim(preg_replace('/\s+/', ' ', $row->full_name)),
                'afiliacion' => $row->afiliacion,
                'estado_html' => "<span class='badge bg-secondary'>N/A</span>",
                'acciones_html' => $actions,
            ];
        })->all();

        return [
            'recordsTotal' => $countTotal,
            'recordsFiltered' => $countFiltered,
            'data' => $data,
        ];
    }

    public function detail(string $hcNumber): ?array
    {
        $patient = DB::table('patient_data')
            ->leftJoinSub($this->coverageSubQuery(), 'cobertura', 'cobertura.hc_number', '=', 'patient_data.hc_number')
            ->select('patient_data.*', DB::raw('COALESCE(cobertura.estado_cobertura, "N/A") as estado_cobertura'))
            ->where('patient_data.hc_number', $hcNumber)
            ->first();

        if (! $patient) {
            return null;
        }

        return [
            'patient' => $patient,
            'coverage' => $patient->estado_cobertura ?? 'N/A',
            'patientAge' => $this->ageFrom($patient->fecha_nacimiento ?? null),
            'consultations' => $this->lastConsultations($hcNumber),
            'solicitudes' => $this->solicitudesList($hcNumber, 10),
            'documentos' => $this->documentosDescargables($hcNumber),
            'diagnosticos' => $this->diagnosticos($hcNumber),
            'doctores' => $this->doctoresAsignados($hcNumber),
            'timelineItems' => $this->timelineItems($hcNumber),
            'eventos' => $this->eventosTimeline($hcNumber),
            'estadisticas' => $this->estadisticasProcedimientos($hcNumber),
        ];
    }

    private function lastConsultations(string $hcNumber): Collection
    {
        return DB::table('consulta_data')
            ->select('form_id', 'fecha', 'diagnosticos')
            ->where('hc_number', $hcNumber)
            ->orderByDesc('fecha')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $diagnostics = collect(json_decode($row->diagnosticos ?? '[]', true))
                    ->pluck('idDiagnostico')
                    ->filter()
                    ->implode(', ');

                return [
                    'form_id' => $row->form_id,
                    'fecha' => $row->fecha,
                    'diagnosticos' => $diagnostics,
                ];
            });
    }

    private function solicitudesList(string $hcNumber, int $limit): Collection
    {
        return DB::table('solicitud_procedimiento')
            ->select('procedimiento', 'created_at', 'tipo', 'form_id')
            ->where('hc_number', $hcNumber)
            ->whereNotNull('procedimiento')
            ->where('procedimiento', '!=', '')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    private function documentosDescargables(string $hcNumber): array
    {
        $protocolos = DB::table('protocolo_data')
            ->select('form_id', 'hc_number', 'membrete', 'fecha_inicio as fecha')
            ->where('hc_number', $hcNumber)
            ->get()
            ->map(fn ($row) => [
                'tipo' => 'protocolo',
                'titulo' => $row->membrete ?? 'Protocolo',
                'fecha' => $row->fecha,
                'hc_number' => $row->hc_number,
                'form_id' => $row->form_id,
            ]);

        $solicitudes = DB::table('solicitud_procedimiento')
            ->select('form_id', 'hc_number', 'procedimiento', 'created_at as fecha')
            ->where('hc_number', $hcNumber)
            ->whereNotNull('procedimiento')
            ->where('procedimiento', '!=', '')
            ->get()
            ->map(fn ($row) => [
                'tipo' => 'solicitud',
                'titulo' => $row->procedimiento ?? 'Solicitud',
                'fecha' => $row->fecha,
                'hc_number' => $row->hc_number,
                'form_id' => $row->form_id,
            ]);

        return $protocolos->merge($solicitudes)
            ->sortByDesc('fecha')
            ->values()
            ->all();
    }

    private function diagnosticos(string $hcNumber): array
    {
        $unique = [];

        foreach ($this->diagnosticosPrefactura($hcNumber) as $diagnostico) {
            $unique[$diagnostico['idDiagnostico']] = $diagnostico;
        }

        $rows = DB::table('consulta_data')
            ->select('fecha', 'diagnosticos')
            ->where('hc_number', $hcNumber)
            ->orderByDesc('fecha')
            ->get();

        foreach ($rows as $row) {
            $lista = json_decode($row->diagnosticos ?? '[]', true);
            $fecha = $row->fecha ? date('d M Y', strtotime($row->fecha)) : null;

            foreach ($lista as $diagnostico) {
                $id = $diagnostico['idDiagnostico'] ?? null;
                if ($id && ! isset($unique[$id])) {
                    $unique[$id] = [
                        'idDiagnostico' => $id,
                        'fecha' => $fecha,
                    ];
                }
            }
        }

        return array_values($unique);
    }

    private function diagnosticosPrefactura(string $hcNumber): array
    {
        if (! $this->hasTable('prefactura_detalle_diagnosticos') || ! $this->hasTable('prefactura_paciente')) {
            return [];
        }

        return DB::table('prefactura_detalle_diagnosticos as d')
            ->join('prefactura_paciente as pp', 'pp.id', '=', 'd.prefactura_id')
            ->select('d.diagnostico_codigo', 'd.descripcion', 'pp.fecha_creacion', 'pp.fecha_registro')
            ->where('pp.hc_number', $hcNumber)
            ->orderByDesc('pp.fecha_creacion')
            ->get()
            ->map(function ($row) {
                $codigo = $row->diagnostico_codigo ?: $row->descripcion;
                if (! $codigo) {
                    return null;
                }

                $fecha = $row->fecha_creacion ?? $row->fecha_registro;

                return [
                    'idDiagnostico' => $codigo,
                    'fecha' => $fecha ? date('d M Y', strtotime($fecha)) : null,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function doctoresAsignados(string $hcNumber): array
    {
        return DB::table('procedimiento_proyectado')
            ->select('doctor', 'form_id')
            ->where('hc_number', $hcNumber)
            ->whereNotNull('doctor')
            ->where('doctor', '!=', '')
            ->orderByDesc('form_id')
            ->limit(10)
            ->get()
            ->unique('doctor')
            ->values()
            ->all();
    }

    private function timelineItems(string $hcNumber): array
    {
        $solicitudes = $this->solicitudesTimelineItems($hcNumber, 100);
        $prefacturas = $this->prefacturaTimelineItems($hcNumber, 100);

        return $this->ordenarTimeline(array_merge($solicitudes, $prefacturas));
    }

    private function solicitudesTimelineItems(string $hcNumber, int $limit): array
    {
        return DB::table('solicitud_procedimiento')
            ->select('procedimiento', 'created_at', 'tipo', 'form_id')
            ->where('hc_number', $hcNumber)
            ->whereNotNull('procedimiento')
            ->where('procedimiento', '!=', '')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'nombre' => $row->procedimiento,
                    'fecha' => $row->created_at,
                    'tipo' => strtolower($row->tipo ?? 'otro'),
                    'form_id' => $row->form_id,
                    'origen' => 'Solicitud',
                ];
            })
            ->all();
    }

    private function prefacturaTimelineItems(string $hcNumber, int $limit): array
    {
        if (! $this->hasTable('prefactura_paciente')) {
            return [];
        }

        return DB::table('prefactura_paciente')
            ->select('id', 'fecha_creacion', 'procedimientos', 'form_id')
            ->where('hc_number', $hcNumber)
            ->orderByDesc('fecha_creacion')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $procedimientos = [];
                if (! empty($row->procedimientos) && is_string($row->procedimientos)) {
                    $procedimientos = json_decode($row->procedimientos, true) ?: [];
                }

                $nombre = 'Prefactura';
                if ($procedimientos !== []) {
                    $nombre .= ' - ' . count($procedimientos) . ' procedimientos';
                }

                return [
                    'nombre' => $nombre,
                    'fecha' => $row->fecha_creacion,
                    'tipo' => 'prefactura',
                    'form_id' => $row->form_id,
                    'origen' => 'Prefactura',
                ];
            })
            ->all();
    }

    private function eventosTimeline(string $hcNumber): array
    {
        return DB::table('procedimiento_proyectado as pp')
            ->leftJoin('consulta_data as cd', function ($join) {
                $join->on('pp.hc_number', '=', 'cd.hc_number')
                    ->on('pp.form_id', '=', 'cd.form_id');
            })
            ->leftJoin('protocolo_data as pr', function ($join) {
                $join->on('pp.hc_number', '=', 'pr.hc_number')
                    ->on('pp.form_id', '=', 'pr.form_id');
            })
            ->select([
                'pp.procedimiento_proyectado',
                'pp.form_id',
                DB::raw('COALESCE(cd.fecha, pr.fecha_inicio) as fecha'),
                DB::raw('COALESCE(cd.examen_fisico, pr.membrete) as contenido'),
            ])
            ->where('pp.hc_number', $hcNumber)
            ->where('pp.procedimiento_proyectado', 'NOT LIKE', '%optometría%')
            ->orderBy('fecha')
            ->get()
            ->filter(fn ($row) => $row->fecha && strtotime($row->fecha))
            ->values()
            ->all();
    }

    private function estadisticasProcedimientos(string $hcNumber): array
    {
        $rows = DB::table('procedimiento_proyectado')
            ->select('procedimiento_proyectado')
            ->where('hc_number', $hcNumber)
            ->get();

        $conteo = [];
        foreach ($rows as $row) {
            $parts = explode(' - ', $row->procedimiento_proyectado ?? '');
            $categoria = strtoupper($parts[0] ?? '');
            $nombre = in_array($categoria, ['CIRUGIAS', 'PNI', 'IMAGENES'], true)
                ? $categoria
                : ($parts[2] ?? $categoria ?: 'Otro');

            $conteo[$nombre] = ($conteo[$nombre] ?? 0) + 1;
        }

        $total = array_sum($conteo);
        if ($total === 0) {
            return [];
        }

        return array_map(fn ($valor) => round(($valor / $total) * 100, 2), $conteo);
    }

    private function ordenarTimeline(array $items): array
    {
        usort($items, static function (array $a, array $b): int {
            return strtotime($b['fecha'] ?? '') <=> strtotime($a['fecha'] ?? '');
        });

        return $items;
    }

    private function ageFrom(?string $date): ?int
    {
        if (! $date) {
            return null;
        }

        try {
            return Carbon::parse($date)->age;
        } catch (\Throwable) {
            return null;
        }
    }

    private function hasPrefactura(): bool
    {
        if ($this->prefacturaTableExists !== null) {
            return $this->prefacturaTableExists;
        }

        try {
            $this->prefacturaTableExists = DB::selectOne("SHOW TABLES LIKE 'prefactura_paciente'") !== null;
        } catch (QueryException) {
            $this->prefacturaTableExists = false;
        }

        return $this->prefacturaTableExists;
    }

    private function hasTable(string $table): bool
    {
        try {
            return DB::selectOne('SHOW TABLES LIKE ?', [$table]) !== null;
        } catch (QueryException) {
            return false;
        }
    }

    private function coverageSubQuery()
    {
        if (! $this->hasPrefactura()) {
            return DB::query()->selectRaw('NULL AS hc_number, NULL AS estado_cobertura')->whereRaw('1 = 0');
        }

        return DB::table('prefactura_paciente as base')
            ->selectRaw("base.hc_number, CASE
                WHEN base.fecha_vigencia IS NULL THEN 'N/A'
                WHEN base.fecha_vigencia >= CURRENT_DATE THEN 'Con Cobertura'
                ELSE 'Sin Cobertura'
            END AS estado_cobertura")
            ->joinSub(
                DB::table('prefactura_paciente')
                    ->selectRaw('hc_number, MAX(fecha_vigencia) AS max_fecha')
                    ->whereNotNull('cod_derivacion')
                    ->where('cod_derivacion', '!=', '')
                    ->groupBy('hc_number'),
                'latest',
                function ($join) {
                    $join
                        ->on('latest.hc_number', '=', 'base.hc_number')
                        ->on('latest.max_fecha', '=', 'base.fecha_vigencia');
                }
            )
            ->whereNotNull('base.cod_derivacion')
            ->where('base.cod_derivacion', '!=', '');
    }
}
