<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->get(); // Her rol için kullanıcı sayısı da alınır


        return view(theme_view('admin', 'pages.roles.index'), compact('roles'));
    }

    public function create() {
        $permissionGroups = config('permissions');

        return view(theme_view('admin', 'pages.roles.create'), compact('permissionGroups'));
    }

    public function edit(Role $role)
    {
        $permissionGroups = config('permissions');
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view(theme_view('admin', 'pages.roles.edit'), compact('permissionGroups', 'rolePermissions', 'role'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route(config('system.admin_prefix').'.roles.index')->with('success', 'Rol başarıyla oluşturuldu.');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route(config('system.admin_prefix').'.roles.index')->with('success', 'Rol başarıyla güncellendi.');
    }


}
