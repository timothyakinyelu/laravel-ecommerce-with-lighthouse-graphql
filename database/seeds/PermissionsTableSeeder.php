<?php

use Illuminate\Database\Seeder;

use App\Permission;
use App\Role;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vendor_role = Role::where('role_key','vendor')->first();
		$administrator_role = Role::where('role_key', 'administrator')->first();
		$registered_role = Role::where('role_key', 'registered')->first();
		$guest_role = Role::where('role_key', 'guest')->first();

		$createCategory = new Permission;
		$createCategory->name = 'Create Categories';
		$createCategory->slug = $createCategory->name;
		$createCategory->save();
		$createCategory->roles()->attach($administrator_role);

		$createProduct = new Permission;
		$createProduct->name = 'Create Products';
		$createProduct->slug = $createProduct->name;
		$createProduct->save();
        $createProduct->roles()->attach($vendor_role);
        
		$viewProducts = new Permission;
		$viewProducts->name = 'View Products';
		$viewProducts->slug = $viewProducts->name;
		$viewProducts->save();
        $viewProducts->roles()->attach($registered_role);
        
		$viewProducts = new Permission;
		$viewProducts->name = 'View Products';
		$viewProducts->slug = $viewProducts->name;
		$viewProducts->save();
		$viewProducts->roles()->attach($guest_role);
    }
}
