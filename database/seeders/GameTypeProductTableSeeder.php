<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameTypeProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gameTypeIdsByCode = DB::table('game_types')
            ->pluck('id', 'code');

        $products = DB::table('products')
            ->select('id', 'game_type', 'product_name')
            ->get();

        $now = now();

        $records = [];
        foreach ($products as $product) {
            $gameTypeId = $gameTypeIdsByCode[$product->game_type] ?? null;

            if (! $gameTypeId) {
                continue;
            }

            $records[] = [
                'product_id' => $product->id,
                'game_type_id' => $gameTypeId,
                'image' => $product->product_name.'.png',
                'rate' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('game_type_product')->delete();

        if ($records !== []) {
            DB::table('game_type_product')->insert($records);
        }
    }
}
