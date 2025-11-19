<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientIdentityCertification extends Model
{
    protected $table = 'patient_identity_certifications';

    protected $casts = [
        'expired_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_verification_at' => 'datetime',
    ];

    protected $fillable = [
        'patient_id',
        'status',
        'expired_at',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'hc_number');
    }
}
