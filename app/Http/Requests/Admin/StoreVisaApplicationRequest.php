<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisaApplicationRequest extends FormRequest
{
    use Ds160ApplicantRules;

    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    public function rules(): array
    {
        return [
            ...$this->ds160ApplicantRules(),
            'photograph' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ];
    }
}