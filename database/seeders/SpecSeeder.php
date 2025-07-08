<?php

namespace Database\Seeders;

use App\Models\Spec;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specs = [
            ['name' => 'USA'],
            ['name' => 'EU'],
            ['name' => 'Russian'],
            ['name' => 'Organic'],
        ];

        foreach ($specs as $spec) {
            Spec::create($spec);
        }
    }
} 