<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Permission;
use App\Role;
use App\User;

class UsersTableSeeder extends Seeder
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
		$vendor_perm = Permission::where('slug','create-products')->first();
		$administrator_perm = Permission::where('slug','create-categories')->first();
		$registered_perm = Permission::where('slug','view-products')->first();
		$guest_perm = Permission::where('slug','view-products')->first();

		$developer = new User;
		$developer->first_name = 'Juniper';
		$developer->last_name = 'Lee';
		$developer->email = 'lee.juniper@xo.com';
        $developer->password = Hash::make('secret');
        $developer->gender = 'Female';
		$developer->save();
		$developer->roles()->attach($vendor_role);
		$developer->permissions()->attach($vendor_perm);

		$manager = new User;
		$manager->first_name = 'Lauretta';
		$manager->last_name = 'Hills';
		$manager->email = 'hills.lauretta@xo.com';
        $manager->password = Hash::make('password');
        $manager->gender = 'Female';
		$manager->save();
		$manager->roles()->attach($administrator_role);
        $manager->permissions()->attach($administrator_perm);
        
		$manager = new User;
		$manager->first_name = 'Larissa';
		$manager->last_name = 'Hills';
		$manager->email = 'hills.larissa@xo.com';
        $manager->password = Hash::make('password');
        $manager->gender = 'Female';
		$manager->save();
		$manager->roles()->attach($registered_role);
        $manager->permissions()->attach($registered_perm);
        
		$manager = new User;
		$manager->first_name = 'Lotana';
		$manager->last_name = 'Hills';
		$manager->email = 'hills.lotana@xo.com';
        $manager->password = Hash::make('password');
        $manager->gender = 'Male';
		$manager->save();
		$manager->roles()->attach($guest_role);
		$manager->permissions()->attach($guest_perm);
    }
}
