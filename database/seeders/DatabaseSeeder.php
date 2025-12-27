<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = config('permissions');

        foreach ($permissions as $group => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate([
                    'name' => $perm
                ]);
            }
        }

        // Rolleri oluştur
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $editor = Role::firstOrCreate(['name' => 'editor']);

        // Yetkileri ata
        $admin->syncPermissions([
            'blog.view', 'blog.edit', 'blog.add',
            'sayfa.view', 'sayfa.edit',
        ]);

        $superadmin->syncPermissions(Permission::all());

        $editor->syncPermissions([
            'blog.view', 'blog.add',
            'sayfa.view',
        ]);
    }
}
