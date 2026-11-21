<?php

namespace App\Http\Requests\Admin;

trait Ds160ApplicantRules
{
    /**
     * @return array<string, mixed>
     */
    protected function ds160ApplicantRules(): array
    {
        return [
            'applicant_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'place_of_birth' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'nationality' => ['required', 'string', 'max:64'],
            'purpose_of_travel' => ['required', 'string', 'max:255'],
            'completed_on' => ['nullable', 'date'],
            'case_created_on' => ['nullable', 'date'],
            'case_updated_on' => ['nullable', 'date'],
            'required_documents' => ['nullable', 'string', 'max:1000'],
            'confirmation_address' => ['nullable', 'string', 'max:1000'],
            'passport_number' => ['required', 'string', 'max:20'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'surname_verification_override' => ['nullable', 'string', 'max:5'],
        ];
    }
}