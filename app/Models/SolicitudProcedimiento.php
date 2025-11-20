<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudProcedimiento extends Model
{
    use HasFactory;

    protected $table = 'solicitud_procedimiento';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'hc_number',
        'procedimiento',
        'created_at',
        'tipo',
        'form_id',
        'fecha',
        'turno',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'created_at' => 'datetime',
        'fecha' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'hc_number', 'hc_number');
    }

    public function scopeForPatient($query, string $hcNumber)
    {
        return $query->where('hc_number', $hcNumber);
    }

    public function scopeWithProcedure($query)
    {
        return $query
            ->whereNotNull('procedimiento')
            ->where('procedimiento', '!=', '');
    }

    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderByDesc('created_at')->limit($limit);
    }
}
