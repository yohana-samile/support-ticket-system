<?php

namespace App\Models\Access;

/**
 * Traits RoleAccess
 * @package App\Models\Auth
 */
trait RoleAccess
{
    /**
     * Save the inputted permissions.
     *
     * @param mixed $inputPermissions
     *
     * @return void
     */
    public function savePermissions($inputPermissions)
    {
        if (! empty($inputPermissions)) {
            $this->permissions()->sync($inputPermissions);
        } else {
            $this->permissions()->detach();
        }
    }

    /**
     * Attach permission to current role.
     *
     * @param object|array $permission
     *
     * @return void
     */
    public function attachPermission($permission)
    {
        if (is_object($permission)) {
            $permission = $permission->getKey();
        }

        if (is_array($permission)) {
            $permission = $permission['id'];
        }

        $this->permissions()->attach($permission);
    }

    /**
     * Detach permission form current role.
     *
     * @param object|array $permission
     *
     * @return void
     */
    public function detachPermission($permission)
    {
        if (is_object($permission)) {
            $permission = $permission->getKey();
        }

        if (is_array($permission)) {
            $permission = $permission['id'];
        }

        $this->permissions()->detach($permission);
    }

    /**
     * Attach multiple permissions to current role.
     *
     * @param mixed $permissions
     *
     * @return void
     */
    public function attachPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            $this->attachPermission($permission);
        }
    }

    /**
     * Detach multiple permissions from current role.
     *
     * @param mixed $permissions
     *
     * @return void
     */
    public function detachPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            $this->detachPermission($permission);
        }
    }

    /**
     * Checks if the role has a Permission by its name or id.
     *
     * @param  string $nameOrId Role name or id.
     * @return bool
     */
    public function hasPermission($nameOrId) {
        foreach ($this->permissions as $permission) {
            //First check to see if it's an ID
            if (is_numeric($nameOrId)) {
                if ($permission->id == $nameOrId) {
                    return true;
                }
            }

            //Otherwise check by name
            if (strtolower($permission->name) == strtolower($nameOrId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks to see if role has array of permissions
     *
     * @param $permissions
     * @param $needsAll
     * @return bool
     */
    public function hasRoles($permissions, $needsAll) {
        //User has to possess all of the roles specified
        if ($needsAll) {
            $hasPermissions = 0;
            $numPermissions = count($permissions);

            foreach ($permissions as $permission) {
                if ($this->hasPermission($permission)) {
                    $hasPermissions++;
                }
            }

            return $numPermissions == $hasPermissions;
        }

        //Role has to possess one of the permissions specified
        $hasPermissions = 0;
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                $hasPermissions++;
            }
        }

        return $hasPermissions > 0;
    }

}
