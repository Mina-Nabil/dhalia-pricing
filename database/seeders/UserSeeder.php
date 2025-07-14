<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'dev',
            'username' => 'dev',
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'password' => Hash::make('dev@smart'),
        ]);
        User::create([
            'name' => 'magdy',
            'username' => 'Magdy El Wahsh',
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'password' => Hash::make('magdy@dhalia'),
        ]);
    }
}
