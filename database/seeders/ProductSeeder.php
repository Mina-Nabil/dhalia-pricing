<?php

namespace Database\Seeders;

use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Models\Spec;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories by name to ensure correct mapping
        $fruitsVegetables = ProductCategory::where('name', 'Fruits & Vegetables')->first();
        $grainsCereals = ProductCategory::where('name', 'Grains & Cereals')->first();
        $herbsSpices = ProductCategory::where('name', 'Herbs & Spices')->first();
        $organicSeeds = ProductCategory::where('name', 'Organic Seeds')->first();

        // Get specs by name to ensure correct mapping
        $usaSpec = Spec::where('name', 'USA')->first();
        $euSpec = Spec::where('name', 'EU')->first();
        $russianSpec = Spec::where('name', 'Russian')->first();
        $organicSpec = Spec::where('name', 'Organic')->first();

        $products = [
            // Fruits & Vegetables (5 products)
            [
                'name' => 'Organic Tomatoes',
                'product_category_id' => $fruitsVegetables->id,
                'base_cost' => 45.00, // per kg
                'spec_id' => $organicSpec->id,
            ],
            [
                'name' => 'Fresh Potatoes',
                'product_category_id' => $fruitsVegetables->id,
                'base_cost' => 18.50, // per kg
                'spec_id' => $russianSpec->id,
            ],
            [
                'name' => 'Sweet Corn',
                'product_category_id' => $fruitsVegetables->id,
                'base_cost' => 35.00, // per kg
                'spec_id' => $usaSpec->id,
            ],
            [
                'name' => 'Bell Peppers',
                'product_category_id' => $fruitsVegetables->id,
                'base_cost' => 55.00, // per kg
                'spec_id' => $euSpec->id,
            ],
            [
                'name' => 'Organic Carrots',
                'product_category_id' => $fruitsVegetables->id,
                'base_cost' => 28.00, // per kg
                'spec_id' => $organicSpec->id,
            ],

            // Grains & Cereals (4 products)
            [
                'name' => 'Premium Wheat Flour',
                'product_category_id' => $grainsCereals->id,
                'base_cost' => 25.00, // per kg
                'spec_id' => $euSpec->id,
            ],
            [
                'name' => 'Brown Rice',
                'product_category_id' => $grainsCereals->id,
                'base_cost' => 42.00, // per kg
                'spec_id' => $usaSpec->id,
            ],
            [
                'name' => 'Organic Quinoa',
                'product_category_id' => $grainsCereals->id,
                'base_cost' => 180.00, // per kg
                'spec_id' => $organicSpec->id,
            ],
            [
                'name' => 'Rolled Oats',
                'product_category_id' => $grainsCereals->id,
                'base_cost' => 65.00, // per kg
                'spec_id' => $russianSpec->id,
            ],

            // Herbs & Spices (3 products)
            [
                'name' => 'Dried Basil',
                'product_category_id' => $herbsSpices->id,
                'base_cost' => 120.00, // per 100g
                'spec_id' => $euSpec->id,
            ],
            [
                'name' => 'Black Pepper Ground',
                'product_category_id' => $herbsSpices->id,
                'base_cost' => 85.00, // per 100g
                'spec_id' => $usaSpec->id,
            ],
            [
                'name' => 'Turmeric Powder',
                'product_category_id' => $herbsSpices->id,
                'base_cost' => 95.00, // per 100g
                'spec_id' => $russianSpec->id,
            ],

            // Organic Seeds (3 products)
            [
                'name' => 'Sunflower Seeds',
                'product_category_id' => $organicSeeds->id,
                'base_cost' => 75.00, // per kg
                'spec_id' => $organicSpec->id,
            ],
            [
                'name' => 'Pumpkin Seeds',
                'product_category_id' => $organicSeeds->id,
                'base_cost' => 150.00, // per kg
                'spec_id' => $organicSpec->id,
            ],
            [
                'name' => 'Chia Seeds',
                'product_category_id' => $organicSeeds->id,
                'base_cost' => 220.00, // per kg
                'spec_id' => $organicSpec->id,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
} 