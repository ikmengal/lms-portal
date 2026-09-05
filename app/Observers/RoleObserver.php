<?php

namespace App\Observers;

use App\Models\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RoleObserver
{
    private static bool $syncing = false;

    public function created(Role $role): void
    {
        $this->syncRolePermissions($role);
    }

    public function updated(Role $role): void
    {
        $this->syncRolePermissions($role);
    }

    private function syncRolePermissions(Role $role): void
    {
        if (self::$syncing) {
            return;
        }
        self::$syncing = true;

        $permissionIds = $role->permissions->pluck('id')->map(fn ($id) => (int) $id)->all();

        DB::table('role_has_permissions')->where('role_id', $role->id)->delete();

        foreach ($permissionIds as $permissionId) {
            DB::table('role_has_permissions')->insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        Cache::forget('spatie.permission.cache');

        self::$syncing = false;
    }
}
