<?php

namespace App\Http\Requests\Legacy;

use Illuminate\Foundation\Http\FormRequest;

class BillingMonthExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'mes' => ['required', 'date_format:Y-m'],
            'grupo' => ['nullable', 'string'],
        ];
    }
}
