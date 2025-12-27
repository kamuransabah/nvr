<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;


// Tüm yetkileri vermek için
//  php artisan db:seed --class=PermissionSeeder



class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = config('permissions');

        // Tüm izinleri oluştur
        foreach ($permissions as $group => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate([
                    'name' => $perm,
                    'guard_name' => 'web'
                ]);
            }
        }

        // Belirli kullanıcıya doğrudan tüm izinleri ver
        $adminEmail = 'kamuran@novarge.com.tr';

        $user = User::where('email', $adminEmail)->first();

        if ($user) {
            $user->givePermissionTo(Permission::all());
            $this->command->info("Tüm yetkiler kullanıcıya atandı: {$adminEmail}");
        } else {
            $this->command->warn("Kullanıcı bulunamadı: {$adminEmail}");
        }
    }
}

