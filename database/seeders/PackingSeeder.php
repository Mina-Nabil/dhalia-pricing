<?php

namespace Database\Seeders;

use App\Models\Packing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packings = [
            [
                'name' => 'Paper',
                'cost' => 5.00,
                'is_active' => true,
            ],
            [
                'name' => 'Poly',
                'cost' => 6.00,
                'is_active' => true,
            ],
            [
                'name' => 'Carton',
                'cost' => 4.00,
                'is_active' => true,
            ],
        ];

        foreach ($packings as $packing) {
            Packing::create($packing);
        }
    }
} 