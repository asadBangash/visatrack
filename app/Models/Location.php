<?php

namespace App\Models;

use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    /** @use HasFactory<LocationFactory> */
    use HasFactory;
    protected $fillable = [
        'country',
        'city',
        'location_code',
        'consulate_title',
        'street_address',
        'display_name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function visaApplications(): HasMany
    {
        return $this->hasMany(VisaApplication::class);
    }

    public function confirmationLocationLabel(): string
    {
        if ($this->location_code && $this->consulate_title) {
            return $this->location_code.', '.$this->consulate_title.'.';
        }

        return $this->display_name;
    }

    public function confirmationAddress(): string
    {
        if ($this->street_address) {
            return $this->street_address;
        }

        return $this->display_name;
    }

    public function statusEmbassyLinkLabel(): string
    {
        return $this->consulate_title ?: $this->display_name;
    }
}