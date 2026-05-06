<?php

namespace Database\Seeders;

use App\Models\PpobTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PpobTransactionSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $price = rand(10000, 100000);
            $selling_price = $price + rand(2000, 5000);
            
            PpobTransaction::create([
                'id' => 'ZSH-PPOB-' . Str::uuid(),
                'user_id' => 1,
                'user_type' => 'App\Models\User',
                'sku' => 'PLN' . $i,
                'target_number' => '08123456789' . $i,
                'price' => $price,
                'selling_price' => $selling_price,
                'status' => 'Success',
                'ref_id' => 'REF-' . Str::random(10),
            ]);
        }
    }
}
