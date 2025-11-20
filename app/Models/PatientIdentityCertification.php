<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientIdentityCertification extends Model
{
    use HasFactory;

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
        'document_number',
        'document_type',
        'signature_path',
        'signature_template',
        'face_image_path',
        'face_template',
        'last_verification_at',
        'last_verification_result',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'hc_number');
    }
}
