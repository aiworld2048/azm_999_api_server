<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameListApiSeeder extends BaseGameListSeeder
{
    public function run(): void
    {
        $operatorCode = config('seamless_key.agent_code');

        if (! $operatorCode) {
            $this->command?->warn('Operator code (AGENT_CODE) is not configured. Skipping GameListApiSeeder.');

            return;
        }

        $products = DB::table('products')
            ->whereNotNull('product_code')
            ->orderBy('id')
            ->get();

        if ($products->isEmpty()) {
            $this->command?->warn('No products found to seed game lists.');

            return;
        }

        foreach ($products as $product) {
            $this->seedFromApi([
                'product_code' => (string) $product->product_code,
                'operator_code' => $operatorCode,
                'game_type' => $product->game_type,
                'provider' => $product->provider,
                'game_list_status' => $product->game_list_status ?? 1,
            ]);
        }
    }
}


