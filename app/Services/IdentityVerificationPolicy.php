<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class IdentityVerificationPolicy
{
    private const DEFAULT_VALIDITY_DAYS = 365;

    public function getValidityDays(): int
    {
        try {
            $value = DB::table('app_settings')
                ->where('name', 'identity_verification_validity_days')
                ->value('value');
        } catch (\Throwable) {
            $value = null;
        }

        if ($value === null) {
            return self::DEFAULT_VALIDITY_DAYS;
        }

        return max(0, (int) $value);
    }
}
