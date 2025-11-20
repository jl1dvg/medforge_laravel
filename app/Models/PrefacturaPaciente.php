<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrefacturaPaciente extends Model
{
    protected $table = 'prefactura_paciente';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'hc_number',
        'cod_derivacion',
        'fecha_vigencia',
        'fecha_creacion',
        'fecha_registro',
        'procedimientos',
        'form_id',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'fecha_vigencia' => 'datetime',
        'fecha_creacion' => 'datetime',
        'fecha_registro' => 'datetime',
        'procedimientos' => 'array',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'hc_number', 'hc_number');
    }

    public function scopeForPatient($query, string $hcNumber)
    {
        return $query->where('hc_number', $hcNumber);
    }
}
