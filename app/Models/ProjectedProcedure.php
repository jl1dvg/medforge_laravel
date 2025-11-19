<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectedProcedure extends Model
{
    use HasFactory;

    protected $table = 'procedimiento_proyectado';

    public $timestamps = false;

    protected $casts = [
        'fecha' => 'datetime',
    ];

    protected $appends = [
        'agenda_date',
        'agenda_time',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'hc_number', 'hc_number');
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class, 'visita_id');
    }

    public function states(): HasMany
    {
        return $this->hasMany(ProjectedProcedureState::class, 'form_id', 'form_id')
            ->orderBy('fecha_hora_cambio');
    }

    public function getAgendaDateAttribute(): ?string
    {
        if ($this->fecha instanceof Carbon) {
            return $this->fecha->toDateString();
        }

        $rawFecha = $this->getAttributeFromArray('fecha');
        if ($rawFecha) {
            try {
                return Carbon::parse($rawFecha)->toDateString();
            } catch (\Throwable) {
                // fallthrough
            }
        }

        $visitDate = $this->visit?->fecha_visita;
        if ($visitDate instanceof Carbon) {
            return $visitDate->toDateString();
        }

        if ($visitDate) {
            try {
                return Carbon::parse($visitDate)->toDateString();
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    public function getAgendaTimeAttribute(): ?string
    {
        $candidates = [
            $this->attributes['hora'] ?? null,
            $this->attributes['fecha'] ?? null,
            $this->visit?->hora_llegada,
        ];

        foreach ($candidates as $value) {
            $normalized = $this->normalizeTime($value);
            if ($normalized !== null) {
                return $normalized;
            }
        }

        return null;
    }

    protected function normalizeTime(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $stringValue = trim((string) $value);
        if ($stringValue === '') {
            return null;
        }

        if (preg_match('/\b(\d{2}:\d{2})(?::\d{2})?\b/u', $stringValue, $matches) === 1) {
            return $matches[1];
        }

        try {
            return Carbon::parse($stringValue)->format('H:i');
        } catch (\Throwable) {
            return null;
        }
    }

    public function getSedeNombreAttribute(): ?string
    {
        $departamento = $this->attributes['sede_departamento'] ?? null;
        $sedeId = $this->attributes['id_sede'] ?? null;

        if ($departamento) {
            return $departamento;
        }

        if ($sedeId) {
            return (string) $sedeId;
        }

        return null;
    }
}
