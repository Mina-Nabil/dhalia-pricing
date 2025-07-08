<?php

namespace Database\Seeders;

use App\Models\Products\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Fruits & Vegetables',
                'description' => 'Fresh and organic fruits and vegetables sourced from local farms',
            ],
            [
                'name' => 'Grains & Cereals',
                'description' => 'High-quality grains, cereals, and flour products for everyday nutrition',
            ],
            [
                'name' => 'Herbs & Spices',
                'description' => 'Premium dried herbs and ground spices for culinary enhancement',
            ],
            [
                'name' => 'Organic Seeds',
                'description' => 'Nutritious and organic seeds for health-conscious consumers',
            ],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }
    }
} 