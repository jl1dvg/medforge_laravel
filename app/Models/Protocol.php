<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Protocol extends Model
{
    use HasFactory;

    protected $table = 'protocolo_data';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'hc_number',
        'membrete',
        'procedimiento_id',
        'fecha_inicio',
        'form_id',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'fecha_inicio' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'hc_number', 'hc_number');
    }

    /**
     * Scope protocols between start and end dates (inclusive).
     */
    public function scopeBetweenDates($query, Carbon $start, Carbon $end)
    {
        return $query->whereBetween('fecha_inicio', [
            $start->copy()->startOfDay(),
            $end->copy()->endOfDay(),
        ]);
    }

    /**
     * Scope by patient identifier.
     */
    public function scopeForPatient($query, string $hcNumber)
    {
        return $query->where('hc_number', $hcNumber);
    }
}
