<?php

use Illuminate\Database\Seeder;

use App\Permission;
use App\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vendor_permission = Permission::where('slug','create-products')->first();
		$administrator_permission = Permission::where('slug', 'create-categories')->first();
		// $registered_permission = Permission::where('slug', 'view-products')->first();
		$guest_permission = Permission::where('slug', 'view-products')->first();

		//RoleTableSeeder.php
		$vendor_role = new Role;
		$vendor_role->role_key = 'vendor';
		$vendor_role->title = 'Admin';
		$vendor_role->save();
        $vendor_role->permissions()->attach($vendor_permission);
        
		$administrator_role = new Role;
		$administrator_role->role_key = 'administrator';
		$administrator_role->title = 'Admin';
		$administrator_role->save();
		$administrator_role->permissions()->attach($administrator_permission);

		$registered_role = new Role;
		$registered_role->role_key = 'registered';
		$registered_role->title = 'Customer';
		$registered_role->save();
        $registered_role->permissions()->attach($guest_permission);
        
		$guest_role = new Role;
		$guest_role->role_key = 'guest';
		$guest_role->title = 'Customer';
		$guest_role->save();
		$guest_role->permissions()->attach($guest_permission);
    }
}
