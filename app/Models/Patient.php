<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patient_data';

    protected $primaryKey = 'hc_number';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $appends = ['full_name'];

    protected $fillable = [
        'hc_number',
        'fname',
        'mname',
        'lname',
        'lname2',
        'afiliacion',
        'celular',
        'fecha_nacimiento',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'hc_number', 'hc_number');
    }

    public function projectedProcedures(): HasMany
    {
        return $this->hasMany(ProjectedProcedure::class, 'hc_number', 'hc_number');
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->attributes['fname'] ?? null,
            $this->attributes['mname'] ?? null,
            $this->attributes['lname'] ?? null,
            $this->attributes['lname2'] ?? null,
        ]);

        $fullName = trim(implode(' ', $parts));

        return $fullName !== '' ? $fullName : Str::of($this->attributes['hc_number'] ?? 'Paciente')->toString();
    }
}
