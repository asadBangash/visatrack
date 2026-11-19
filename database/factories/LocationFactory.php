<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        $country = fake()->country();
        $city = fake()->city();

        return [
            'country' => $country,
            'city' => $city,
            'display_name' => strtoupper($country . ', ' . $city),
            'is_active' => true,
        ];
    }
}