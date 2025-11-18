<?php

namespace Database\Seeders;

use App\Models\Admin\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Agent management (Owner)
            ['title' => 'agent_view', 'group' => 'agent'],
            ['title' => 'agent_create', 'group' => 'agent'],
            ['title' => 'agent_update', 'group' => 'agent'],
            ['title' => 'agent_delete', 'group' => 'agent'],

            // Banner and banner text (Owner)
            ['title' => 'banner_view', 'group' => 'banner'],
            ['title' => 'banner_create', 'group' => 'banner'],
            ['title' => 'banner_update', 'group' => 'banner'],
            ['title' => 'banner_delete', 'group' => 'banner'],

            ['title' => 'banner_text_view', 'group' => 'banner_text'],
            ['title' => 'banner_text_create', 'group' => 'banner_text'],
            ['title' => 'banner_text_update', 'group' => 'banner_text'],
            ['title' => 'banner_text_delete', 'group' => 'banner_text'],

            // Promotions (Owner)
            ['title' => 'promotion_view', 'group' => 'promotion'],
            ['title' => 'promotion_create', 'group' => 'promotion'],
            ['title' => 'promotion_update', 'group' => 'promotion'],
            ['title' => 'promotion_delete', 'group' => 'promotion'],

            // Slot game settings (Owner)
            ['title' => 'slot_setting_view', 'group' => 'slot'],
            ['title' => 'slot_setting_update', 'group' => 'slot'],

            // Owner wallet actions with agents
            ['title' => 'agent_wallet_deposit', 'group' => 'agent_wallet'],
            ['title' => 'agent_wallet_withdraw', 'group' => 'agent_wallet'],

            // Reports
            ['title' => 'report_accept', 'group' => 'report'],

            // Player management (Agent)
            ['title' => 'player_view', 'group' => 'player'],
            ['title' => 'player_create', 'group' => 'player'],
            ['title' => 'player_update', 'group' => 'player'],
            ['title' => 'player_delete', 'group' => 'player'],
            ['title' => 'player_ban', 'group' => 'player'],
            ['title' => 'player_password_change', 'group' => 'player'],

            // Bank management (Agent)
            ['title' => 'bank_view', 'group' => 'bank'],
            ['title' => 'bank_create', 'group' => 'bank'],
            ['title' => 'bank_update', 'group' => 'bank'],
            ['title' => 'bank_delete', 'group' => 'bank'],

            // Player self-service (Player)
            ['title' => 'player_profile_view', 'group' => 'self'],
            ['title' => 'player_profile_update', 'group' => 'self'],
            ['title' => 'player_wallet_view', 'group' => 'self'],
        ];

        $timestamped = collect($permissions)->map(function (array $permission) {
            return array_merge($permission, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        })->toArray();

        Permission::insert($timestamped);
    }
}
