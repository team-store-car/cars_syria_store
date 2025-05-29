<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // إنشاء الأدوار
        $admin = Role::create(['name' => 'admin']);
        $user = Role::create(['name' => 'user']);
        $workshop = Role::create(['name' => 'workshop']);
        $shopManager = Role::create(['name' => 'shop_manager']);

        // إنشاء الصلاحيات
        Permission::create(['name' => 'manage-users']);
        Permission::create(['name' => 'approve-warranty']);
        Permission::create(['name' => 'edit-cars']);
        Permission::create(['name' => 'view-dashboard']);

        // ربط الصلاحيات بالأدوار
        $admin->givePermissionTo(['manage-users', 'view-dashboard']);
        $workshop->givePermissionTo(['approve-warranty']);
        $shopManager->givePermissionTo(['edit-cars']);
    }
}