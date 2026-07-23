<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => env('ROOT_NAME', 'Root'),
            'email' => env('ROOT_EMAIL', 'root@menuhub.com'),
            'password' => Hash::make(env('ROOT_PASSWORD', 'password')),
            'role' => 'root',
            'restaurant_id' => null,
        ]);
    }
}
