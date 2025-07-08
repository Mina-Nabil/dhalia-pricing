<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CurrencySeeder::class,
            SpecSeeder::class,
            PackingSeeder::class
        ]);

        if (app()->environment('local')) {
            $this->call([
                CategorySeeder::class,
                ProductSeeder::class,
            ]);
        }
    }
}
