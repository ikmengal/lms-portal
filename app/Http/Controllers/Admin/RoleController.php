<?php

namespace App\Http\Controllers\Admin;

use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public const SYSTEM_ROLES = ['admin', 'instructor', 'student', 'user'];

    public function index()
    {
        return view('pages.admin.roles.index', [
            'roles' => Role::withCount(['users', 'permissions'])->orderBy('id')->get(),
        ]);
    }

    public function create()
    {
        return view('pages.admin.roles.form', [
            'role' => new Role(),
            'groups' => $this->permissionGroups(),
            'assignedIds' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($request->input('permissions', []));

        $this->flushCache();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->name}\" created with " . count((array) $request->input('permissions', [])) . ' permissions.');
    }

    public function edit(Role $role)
    {
        return view('pages.admin.roles.form', [
            'role' => $role,
            'groups' => $this->permissionGroups(),
            'assignedIds' => $role->permissions->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $data = $this->validated($request, $role->id);

        // System roles keep their name (code checks these by name)
        $name = in_array($role->name, self::SYSTEM_ROLES, true) ? $role->name : $data['name'];

        $role->update(['name' => $name]);
        $role->syncPermissions($request->input('permissions', []));

        $this->flushCache();

        return redirect()->route('admin.roles.index')->with('success', "Role \"{$role->name}\" updated.");
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->with('error', "\"{$role->name}\" is assigned to {$role->users()->count()} user(s). Remove it from those users first.");
        }

        $role->delete();
        $this->flushCache();

        return back()->with('success', "Role \"{$role->name}\" deleted.");
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($ignoreId),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')],
        ]);
    }

    private function permissionGroups(): array
    {
        return Permission::orderBy('name')->get()
            ->groupBy(fn ($p) => \Illuminate\Support\Str::of($p->name)->trim()->explode(' ')->last())
            ->map(fn ($perms) => $perms->values())
            ->sortKeys()
            ->all();
    }

    private function flushCache(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        Cache::forget('spatie.permission.cache');
    }
}
