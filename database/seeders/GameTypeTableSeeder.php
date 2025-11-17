<?php

namespace Database\Seeders;

use App\Models\GameType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $gameTypes = [
            ['code' => 'SHAN', 'name' => 'Shankomee Game', 'name_mm' => 'မြန်မာ့ရှမ်းကိုးမီး', 'img' => 'jackpot.png', 'status' => 1, 'order' => '1'],
            ['code' => 'PONEWINE', 'name' => 'PoneWine Game', 'name_mm' => 'မြန်မာ့ပုံဝိုင်း', 'img' => 'live_casino.png', 'status' => 1, 'order' => '2'],
            ['code' => 'BUFFALO', 'name' => 'Myanmar Buffalo', 'name_mm' => 'မြန်မာ့ ကျွဲဂိမ်း', 'img' => 'sportbook.png', 'status' => 1, 'order' => '3'],
            
        ];

        foreach ($gameTypes as $gameTypeData) {
            GameType::create($gameTypeData);
        }
    }
}
