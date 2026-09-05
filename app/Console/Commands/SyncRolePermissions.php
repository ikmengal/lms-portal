<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SyncRolePermissions extends Command
{
    protected $signature = 'permissions:sync-roles';

    protected $description = 'Sync permissions for all existing roles in role_has_permissions table';

    public function handle(): int
    {
        $roles = Role::with('permissions')->get();

        if ($roles->isEmpty()) {
            $this->warn('No roles found.');
            return self::FAILURE;
        }

        foreach ($roles as $role) {
            $permissionIds = $role->permissions->pluck('id')->map(fn ($id) => (int) $id)->all();

            DB::table('role_has_permissions')->where('role_id', $role->id)->delete();

            foreach ($permissionIds as $permissionId) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permissionId,
                    'role_id' => $role->id,
                ]);
            }

            $this->info("Synced {$role->permissions->count()} permissions for role: {$role->name}");
        }

        $this->syncModelHasPermissions();

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        Cache::forget('spatie.permission.cache');

        $this->info('All role permissions synced successfully.');
        return self::SUCCESS;
    }

    private function syncModelHasPermissions(): void
    {
        DB::table('model_has_permissions')->truncate();

        $users = User::with('roles.permissions')->get();

        $rows = [];
        foreach ($users as $user) {
            foreach ($user->roles as $role) {
                foreach ($role->permissions as $permission) {
                    $rows[] = [
                        'permission_id' => $permission->id,
                        'model_type' => User::class,
                        'model_id' => $user->id,
                    ];
                }
            }
        }

        if (!empty($rows)) {
            DB::table('model_has_permissions')->insert($rows);
        }

        $this->info('Synced ' . DB::table('model_has_permissions')->count() . ' user permissions.');
    }
}
