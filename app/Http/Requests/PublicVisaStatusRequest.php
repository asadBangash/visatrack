<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicVisaStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visa_type' => ['required', 'in:NIV,IV'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'confirmation_number' => ['required', 'string', 'max:32'],
            'passport_number' => ['required', 'string', 'max:20'],
            'surname' => ['required', 'string', 'max:5'],
            'captcha' => ['required', 'string', 'max:5'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'confirmation_number' => strtoupper(trim((string) $this->input('confirmation_number'))),
            'surname' => strtoupper(trim((string) $this->input('surname'))),
        ]);
    }
}