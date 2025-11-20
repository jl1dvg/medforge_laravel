<?php

namespace App\Services;

use App\Models\Patient;

class PatientNameService
{
    public function getFullName(string $hcNumber): string
    {
        $patient = Patient::query()
            ->select(['fname', 'mname', 'lname', 'lname2'])
            ->find($hcNumber);

        if (! $patient) {
            return 'Desconocido';
        }

        $parts = array_filter([
            $patient->lname,
            $patient->lname2,
            $patient->fname,
            $patient->mname,
        ]);

        return trim(preg_replace('/\s+/', ' ', implode(' ', $parts)));
    }
}
