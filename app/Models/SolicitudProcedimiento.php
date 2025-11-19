<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudProcedimiento extends Model
{
    protected $table = 'solicitud_procedimiento';

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
