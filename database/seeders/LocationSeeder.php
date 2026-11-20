<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/locations_raw.txt');
        if (! is_file($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $displayName) {
            $parts = array_map('trim', explode(',', $displayName, 2));
            $country = $parts[0] ?? $displayName;
            $city = $parts[1] ?? $country;

            Location::query()->updateOrCreate(
                ['display_name' => $displayName],
                [
                    'country' => $country,
                    'city' => $city,
                    'is_active' => true,
                ]
            );
        }

        Location::query()->where('display_name', 'PAKISTAN, KARACHI')->update([
            'location_code' => 'KHI',
            'consulate_title' => 'U.S. Consulate General Karachi',
            'street_address' => 'Plot NO 3, 4, 5 New TPX Area, Mai Kolachi Road, Karachi Pakistan',
        ]);

        Location::query()->where('display_name', 'PAKISTAN, LAHORE')->update([
            'location_code' => 'LHR',
            'consulate_title' => 'U.S. Consulate General Lahore',
            'street_address' => '50, Shahrah-e-Abdul Hameed Bin Badees (Old Empress Road), Lahore, Pakistan',
        ]);

        Location::query()->where('display_name', 'PAKISTAN, ISLAMABAD')->update([
            'location_code' => 'ISB',
            'consulate_title' => 'U.S. Embassy Islamabad',
            'street_address' => 'Diplomatic Enclave, Ramna 5, Islamabad, Pakistan',
        ]);

        Location::query()->where('display_name', 'PAKISTAN, PESHAWAR')->update([
            'consulate_title' => 'U.S. Consulate General Peshawar',
            'street_address' => 'Fort Road, Peshawar, Pakistan',
        ]);
    }
}