<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => config('visa.demo_admin_email')],
            [
                'name' => 'Demo Administrator',
                // Plain string: User model uses the "hashed" cast (Hash::make here would double-hash on save).
                'password' => env('DEMO_ADMIN_PASSWORD', 'DemoAdmin!ChangeMe'),
                'is_admin' => true,
            ]
        );

        $this->call([
            LocationSeeder::class,
            VisaDemoSeeder::class,
        ]);
    }
}