<?php

namespace App\Http\Requests\Legacy;

use Illuminate\Foundation\Http\FormRequest;

class BillingExcelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'form_id' => ['required', 'string'],
            'grupo' => ['nullable', 'string'],
        ];
    }
}
