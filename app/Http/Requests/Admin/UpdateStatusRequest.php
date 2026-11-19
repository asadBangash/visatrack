<?php

namespace App\Http\Requests\Admin;

use App\Models\VisaApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                VisaApplication::STATUS_APPLICATION_RECEIVED,
                VisaApplication::STATUS_APPROVED,
                VisaApplication::STATUS_ISSUED,
            ])],
        ];
    }
}