<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisaApplication extends Model
{
    public const STATUS_APPLICATION_RECEIVED = 'application_received';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_ISSUED = 'issued';

    protected $fillable = [
        'confirmation_number',
        'applicant_name',
        'date_of_birth',
        'place_of_birth',
        'gender',
        'nationality',
        'purpose_of_travel',
        'completed_on',
        'case_created_on',
        'case_updated_on',
        'required_documents',
        'confirmation_address',
        'passport_number',
        'passport_lookup_hash',
        'surname_verification_hash',
        'surname_verification_override',
        'photograph_path',
        'location_id',
        'visa_type',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'passport_number' => 'encrypted',
            'date_of_birth' => 'date',
            'completed_on' => 'date',
            'case_created_on' => 'date',
            'case_updated_on' => 'date',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(VisaStatusHistory::class);
    }

    public function statusLabel(): string
    {
        return config('visa.statuses')[$this->status] ?? $this->status;
    }

    public function formattedDateOfBirth(): string
    {
        return $this->date_of_birth?->format('m/d/Y') ?? '';
    }

    public function formattedCompletedOn(): string
    {
        $date = $this->completed_on ?? $this->created_at;

        return strtoupper($date->format('d M Y'));
    }

    public function formattedCaseCreatedOn(): string
    {
        $date = $this->case_created_on ?? $this->created_at;

        return $date->format('d-M-Y');
    }

    public function formattedCaseUpdatedOn(): string
    {
        $date = $this->case_updated_on ?? $this->updated_at;

        return $date->format('d-M-Y');
    }

    public function genderLabel(): string
    {
        return match (strtolower((string) $this->gender)) {
            'male', 'm' => 'Male',
            'female', 'f' => 'Female',
            default => ucfirst((string) $this->gender),
        };
    }

    public function requiredDocumentsText(): string
    {
        return $this->required_documents
            ?: config('visa.default_required_documents');
    }

    public function confirmationAddressLine(): string
    {
        if ($this->confirmation_address) {
            return $this->confirmation_address;
        }

        return $this->location?->confirmationAddress() ?? '';
    }

    public function maskedPassport(): string
    {
        $plain = $this->passport_number;
        if (strlen($plain) <= 4) {
            return str_repeat('•', strlen($plain));
        }

        return str_repeat('•', strlen($plain) - 4) . substr($plain, -4);
    }
}