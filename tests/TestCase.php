<?php

namespace Tests;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Contracts\Auth\Access\Gate;
// use Illuminate\Support\Facades\Gate;

use App\Permission;
use App\Role;
use App\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MakesGraphQLRequests;

    /**
     * Logs a user in with specified permission(s).
     *
     * @param $permissions
     * @return mixed|null
     */
    public function loginWithPermission($permissions)
    {
        $user = $this->userWithPermissions($permissions);

        $this->definePermissions();

        $this->actingAs($user);

        return $user;
    }

    /**
     * Create user with permissions.
     *
     * @param $permissions
     * @param null $user
     * @return mixed|null
     */
    private function userWithPermissions($permissions, $user = null)
    {
        if(is_string($permissions)) {
            $permission = factory(Permission::class)->create(['slug'=>$permissions, 'name'=>ucwords(str_replace('-', ' ', $permissions))]);

            if (!$user) {
                $role = factory(Role::class)->create(['role_key'=>'administrator', 'title'=>'Admin']);

                $user = factory(User::class)->create();
                $user->roles()->attach($role);
            } else {
                $role = $user->roles->first();
            }
            $role->permissions()->attach($permission);
        } else {
            foreach($permissions as $permission) {
                $user = $this->userWithPermissions($permission, $user);
            }
        }

        return $user;
    }


    /**
     * Registers defined permissions.
     */
    private function definePermissions()
    {
        try {
            Permission::get()->map(function ($permission) {
                $gate = $this->app->make(Gate::class);
                $gate->define($permission->slug, function ($user) use ($permission) {
                    return $user->hasPermissionTo($permission);
                });
            });
        } catch(\Excpetion $e) {
            report($e);
            return false;
        };
    }
}
