<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Tambahkan roles
        $admin = Role::create(['name' => 'admin']);
        $user = Role::create(['name' => 'user']);
        $superuser = Role::create(['name' => 'superuser']);

        // Tambahkan permissions
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'view reports']);

        // Tetapkan permissions ke roles
        $admin->givePermissionTo('manage users');
        $user->givePermissionTo('view reports');
    }
}