<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = Role::create(['name' => 'super admin']);
        $agent = Role::create(['name' => 'agent']);
        $messenger = Role::create(['name' => 'messenger']);
        $user = Role::create(['name' => 'user']);

        Permission::create(['name' => 'location-view']);

        Permission::create(['name' => 'category-view']);
        Permission::create(['name' => 'category-add']);
        Permission::create(['name' => 'category-edit']);
        Permission::create(['name' => 'category-delete']);
        
        Permission::create(['name' => 'shipment-view']);

        Permission::create(['name' => 'receiving-view']);

        Permission::create(['name' => 'user-view']);
        Permission::create(['name' => 'user-add']);
        Permission::create(['name' => 'user-edit']);
        Permission::create(['name' => 'user-import']);
        Permission::create(['name' => 'user-reset-password']);
        Permission::create(['name' => 'user-deactivate']);
        Permission::create(['name' => 'user-activate']);

        Permission::create(['name' => 'role-view']);
        Permission::create(['name' => 'role-add']);
        Permission::create(['name' => 'role-edit']);
        Permission::create(['name' => 'role-delete']);
        Permission::create(['name' => 'role-permission']);
        Permission::create(['name' => 'role-edit-permission']);

        Permission::create(['name' => 'logActivity-view']);

        $superadmin->givePermissionTo('location-view');
        
        $superadmin->givePermissionTo('category-view');
        $superadmin->givePermissionTo('category-add');
        $superadmin->givePermissionTo('category-edit');
        $superadmin->givePermissionTo('category-delete');
        
        $superadmin->givePermissionTo('shipment-view');

        $superadmin->givePermissionTo('receiving-view');

        $superadmin->givePermissionTo('user-view');
        $superadmin->givePermissionTo('user-add');
        $superadmin->givePermissionTo('user-edit');
        $superadmin->givePermissionTo('user-import');
        $superadmin->givePermissionTo('user-reset-password');
        $superadmin->givePermissionTo('user-deactivate');
        $superadmin->givePermissionTo('user-activate');

        $superadmin->givePermissionTo('role-view');
        $superadmin->givePermissionTo('role-add');
        $superadmin->givePermissionTo('role-edit');
        $superadmin->givePermissionTo('role-delete');
        $superadmin->givePermissionTo('role-permission');
        $superadmin->givePermissionTo('role-edit-permission');

        $superadmin->givePermissionTo('logActivity-view');
    }
}