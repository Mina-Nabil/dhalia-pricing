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
                'name' => 'Standard Box',
                'cost' => 5.00,
                'is_active' => true,
            ],
            [
                'name' => 'Premium Gift Box',
                'cost' => 12.50,
                'is_active' => true,
            ],
            [
                'name' => 'Eco-Friendly Package',
                'cost' => 8.75,
                'is_active' => true,
            ],
        ];

        foreach ($packings as $packing) {
            Packing::create($packing);
        }
    }
} 