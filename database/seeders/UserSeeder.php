<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'josephmbula2@gmail.com'],
            [
                'name' => 'Joseph Mbula',
                'password' => Hash::make(env('SEEDED_USER_PASSWORD', 'password')),
            ],
        );
    }
}
