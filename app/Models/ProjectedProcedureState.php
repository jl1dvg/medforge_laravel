<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectedProcedureState extends Model
{
    use HasFactory;

    protected $table = 'procedimiento_proyectado_estado';

    public $timestamps = false;

    protected $casts = [
        'fecha_hora_cambio' => 'datetime',
    ];

    protected $fillable = [
        'form_id',
        'estado',
        'fecha_hora_cambio',
    ];

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(ProjectedProcedure::class, 'form_id', 'form_id');
    }
}
