<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'provider' => 'MyanmarGame',
                'currency' => 'MMK',
                'status' => '1',
                'game_type_id' => 1,
                'product_code' => 'Shan001',
                'product_name' => 'Shankomee',
                'short_name' => 'Shan',
                'order' => 1,
                'game_list_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'provider' => 'MyanmarGame',
                'currency' => 'MMK',
                'status' => '1',
                'game_type_id' => 1,
                'product_code' => 'PONE002',
                'product_name' => 'PONEWINE',
                'short_name' => 'PW',
                'order' => 2,
                'game_list_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'provider' => 'MyanmarGame',
                'currency' => 'MMK',
                'status' => '1',
                'game_type_id' => 3,
                'product_code' => 'BUFFALO003',
                'product_name' => 'Jungle King',
                'short_name' => 'Jungle',
                'order' => 3,
                'game_list_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
