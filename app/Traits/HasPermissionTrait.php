<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Permission;
use App\Role;

/**
 * Extends user model functionality
 * for access control
*/
trait HasPermissionTrait
{

    /**
     * Assigns permission to a user if permission exists 
     * @return $permissions or null
    */
    public function givePermissionsTo(...$perms)
    {
        $permissions = $this->getAllPermissions($perms);
        if($permissions === null) {
            return $this;
        }

        $this->permissions()->saveMany($permissions);
        return $this;
    }

    /**
     * Revokes user permission
     * @return $this
    */
    public function revokePermissionsFrom(...$perms)
    {
        $permissions = $this->getAllPermissions($perms);

        $this->permissions()->detach($permissions);
        return $this;
    }

    /**
     * Removes all permissions of user
     * and assigns new permissions
     * @return $this
    */
    public function refreshPermissions(...$perms)
    {
        $this->permissions()->detach();
        return $this->givePermissionsTo($perms);
    }

    /**
     * Checks if user has permission through its role or assigned permission
     * @return bool
    */
    public function hasPermissionTo($perm)
    {
        return $this->hasPermissionThroughRole($perm) || $this->hasPermission($perm);
    }

    /**
     * Checks if user has permission through its role
     * @return bool
    */
    public function hasPermissionThroughRole($perm)
    {
        if(is_string($perm)) {
            $permission = Permission::where('slug', $perm->slug)->first();
            $role = $permission->roles->first();
            if($this->roles->contains($role)) {
                return true;
            }
        } else {
            foreach ($perm->roles as $role){
                if($this->roles->contains($role)) {
                  return true;
                }
            }
        }
        return false;
    }

    /**
     * Checks if user has required role
     * @return bool
    */
    public function hasRole(...$roles)
    {
        foreach($roles as $role) {
            if($this->roles->contains('role_key', $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if permission exists within the array
     * @return $permissions
    */
    protected function getAllPermissions(array $perms)
    {
        return Permission::whereIn('slug', $perms)->get();
    }

    /**
     * Checks if user has a permission
     * @return bool
    */
    protected function hasPermission($perm)
    {
        return (bool) $this->permissions()->where('slug', $perm->slug)->count();
    }

    //Relationships
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
