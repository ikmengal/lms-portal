<?php

namespace App\Http\Controllers\Admin;

use Spatie\Permission\Models\Permission;
use Spatie\Activitylog\Facades\Activity;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();

        return view('pages.admin.permissions.index', [
            'groups' => $permissions
                ->groupBy(fn ($p) => Str::of($p->name)->trim()->explode(' ')->last())
                ->sortKeys(),
            'rolesWithCounts' => Role::withCount('permissions')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $permission = Permission::create(['name' => $data['name'], 'guard_name' => 'web']);
        Activity::causedBy(auth()->user())->performedOn($permission)->event('created')->log('Permission created');

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('success', "Permission \"{$data['name']}\" created.");
    }

    public function update(Request $request, int $id)
    {
        $permission = Permission::findOrFail($id);
        $data = $this->validated($request, $id);

        $permission->update(['name' => $data['name']]);
        Activity::causedBy(auth()->user())->performedOn($permission)->event('updated')->log('Permission updated');

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('success', 'Permission updated.');
    }

    public function destroy(int $id)
    {
        $permission = Permission::findOrFail($id);

        DB::table('role_has_permissions')->where('permission_id', $permission->id)->delete();
        DB::table('model_has_permissions')->where('permission_id', $permission->id)->delete();
        Activity::causedBy(auth()->user())->performedOn($permission)->event('deleted')->log('Permission deleted');
        $permission->delete();

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('success', "Permission \"{$permission->name}\" deleted and removed from all roles.");
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                'regex:/^[a-zA-Z0-9 ]+$/',
                Rule::unique('permissions', 'name')->where('guard_name', 'web')->ignore($ignoreId),
            ],
        ]);
    }
}
