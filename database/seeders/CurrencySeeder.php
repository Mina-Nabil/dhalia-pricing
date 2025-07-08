<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'US Dollar',
                'code' => 'USD',
                'rate' => 49.60, // 1 USD = 49.60 EGP
            ],
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'rate' => 53.90, // 1 EUR = 53.90 EGP
            ],
            [
                'name' => 'Egyptian Pound',
                'code' => 'EGP',
                'rate' => 1, // Base currency
            ],
            [
                'name' => 'Russian Ruble',
                'code' => 'RUB',
                'rate' => 1.58, // 1 EGP = 7 RUB
            ],
            [
                'name' => 'UAE Dirham',
                'code' => 'AED',
                'rate' => 13.67, // 1 EGP = 13.67 AED
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
} 