<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visit extends Model
{
    use HasFactory;

    protected $table = 'visitas';

    public $timestamps = false;

    protected $casts = [
        'fecha_visita' => 'datetime',
        'hora_llegada' => 'datetime',
    ];

    protected $fillable = [
        'hc_number',
        'fecha_visita',
        'hora_llegada',
        'usuario_registro',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'hc_number', 'hc_number');
    }

    public function projectedProcedures(): HasMany
    {
        return $this->hasMany(ProjectedProcedure::class, 'visita_id');
    }
}
