<?php

namespace Database\Seeders;

use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionRoleTableSeeder extends Seeder
{
    private const ROLE_PERMISSIONS = [
        'Owner' => [
            'agent_view',
            'agent_create',
            'agent_update',
            'agent_delete',
            'banner_view',
            'banner_create',
            'banner_update',
            'banner_delete',
            'banner_text_view',
            'banner_text_create',
            'banner_text_update',
            'banner_text_delete',
            'promotion_view',
            'promotion_create',
            'promotion_update',
            'promotion_delete',
            'slot_setting_view',
            'slot_setting_update',
            'agent_wallet_deposit',
            'agent_wallet_withdraw',
            'report_accept',
        ],
        'Agent' => [
            'player_view',
            'player_create',
            'player_update',
            'player_delete',
            'player_ban',
            'player_password_change',
            'bank_view',
            'bank_create',
            'bank_update',
            'bank_delete',
        ],
        'Player' => [
            'player_profile_view',
            'player_profile_update',
            'player_wallet_view',
        ],
    ];

    private const ROLE_IDS = [
        'Owner' => 1,
        'Agent' => 2,
        'Player' => 3,
    ];

    public function run(): void
    {
        try {
            DB::beginTransaction();

            // Validate roles exist
            $this->validateRoles();

            // Validate permissions exist
            $this->validatePermissions();

            // Clean up existing permission assignments
            $this->cleanupExistingAssignments();

            // Assign permissions to roles
            foreach (self::ROLE_PERMISSIONS as $roleName => $permissions) {
                $roleId = self::ROLE_IDS[$roleName];
                $permissionIds = Permission::whereIn('title', $permissions)
                    ->pluck('id')
                    ->toArray();

                $this->assignPermissions($roleId, $permissionIds, $roleName);
            }

            // Verify permission assignments
            $this->verifyPermissionAssignments();

            DB::commit();
            Log::info('Permission assignments completed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in PermissionRoleTableSeeder: '.$e->getMessage());
            throw $e;
        }
    }

    private function validateRoles(): void
    {
        $existingRoles = Role::whereIn('id', array_values(self::ROLE_IDS))->pluck('id')->toArray();
        $missingRoles = array_diff(array_values(self::ROLE_IDS), $existingRoles);

        if (! empty($missingRoles)) {
            throw new \RuntimeException('Missing required roles with IDs: '.implode(', ', $missingRoles));
        }
    }

    private function validatePermissions(): void
    {
        $allPermissions = array_merge(...array_values(self::ROLE_PERMISSIONS));
        $existingPermissions = Permission::whereIn('title', $allPermissions)->pluck('title')->toArray();
        $missingPermissions = array_diff($allPermissions, $existingPermissions);

        if (! empty($missingPermissions)) {
            throw new \RuntimeException('Missing required permissions: '.implode(', ', $missingPermissions));
        }
    }

    private function cleanupExistingAssignments(): void
    {
        try {
            DB::table('permission_role')->delete();
            Log::info('Cleaned up existing permission assignments');
        } catch (\Exception $e) {
            Log::error('Failed to cleanup existing permission assignments: '.$e->getMessage());
            throw $e;
        }
    }

    private function assignPermissions(int $roleId, array $permissions, string $roleName): void
    {
        try {
            $role = Role::findOrFail($roleId);
            $role->permissions()->sync($permissions);
            Log::info('Assigned '.count($permissions)." permissions to {$roleName} role");
        } catch (\Exception $e) {
            Log::error("Failed to assign permissions to {$roleName} role: ".$e->getMessage());
            throw $e;
        }
    }

    private function verifyPermissionAssignments(): void
    {
        foreach (self::ROLE_PERMISSIONS as $roleName => $expectedPermissions) {
            $roleId = self::ROLE_IDS[$roleName];
            $role = Role::findOrFail($roleId);
            $assignedPermissions = $role->permissions()->pluck('title')->toArray();
            $missingPermissions = array_diff($expectedPermissions, $assignedPermissions);

            if (! empty($missingPermissions)) {
                throw new \RuntimeException(
                    "Role '{$roleName}' is missing permissions: ".implode(', ', $missingPermissions)
                );
            }
        }
    }
}
