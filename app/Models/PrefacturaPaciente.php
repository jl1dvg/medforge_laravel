<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrefacturaPaciente extends Model
{
    protected $table = 'prefactura_paciente';

    public $timestamps = false;

    protected $casts = [
        'fecha_vigencia' => 'datetime',
        'fecha_creacion' => 'datetime',
        'fecha_registro' => 'datetime',
    ];
}
