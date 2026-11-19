<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => config('visa.demo_admin_email')],
            [
                'name' => 'Demo Administrator',
                'password' => Hash::make(env('DEMO_ADMIN_PASSWORD', 'DemoAdmin!ChangeMe')),
                'is_admin' => true,
            ]
        );

        $this->call([
            LocationSeeder::class,
            VisaDemoSeeder::class,
        ]);
    }
}