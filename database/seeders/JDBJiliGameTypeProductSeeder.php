<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class JDBJiliGameTypeProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $data = [

            
            // Product 26: JDB - FISHING (game_type_id: 8)
           ['product_id' => 26, 'game_type_id' => 8, 'image' => 'JDBFishing.png', 'rate' => 1.0000],
            
            // Product 27: Jili - FISHING (game_type_id: 8)
            ['product_id' => 27, 'game_type_id' => 8, 'image' => 'JiliFishing.png', 'rate' => 1.0000],
        ];

        DB::table('game_type_product')->insert($data);
    }
}
