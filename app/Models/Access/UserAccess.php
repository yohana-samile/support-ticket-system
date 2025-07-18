<?php

namespace App\Models\Access;

trait UserAccess
{

    /**
     * Checks if the user has a Role by its name or id.
     *
     * @param  string $nameOrId Role name or id.
     * @return bool
     */
    public function hasRole($nameOrId) {
        foreach ($this->roles as $role) {
            //First check to see if it's an ID
            if (is_numeric($nameOrId)) {
                if ($role->role_id == $nameOrId) {
                    return true;
                }
            }

            //Otherwise check by name
            if (strtolower($role->name) == strtolower($nameOrId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks to see if user has array of roles
     *
     * All must return true
     * @param  $roles
     * @param  $needsAll
     * @return bool
     */
    public function hasRoles($roles, $needsAll) {
        //User has to possess all the roles specified
        if ($needsAll) {
            $hasRoles = 0;
            $numRoles = count($roles);

            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    $hasRoles++;
                }
            }

            return $numRoles == $hasRoles;
        }

        //User has to possess one of the roles specified
        $hasRoles = 0;
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                $hasRoles++;
            }
        }

        return $hasRoles > 0;
    }

    /**
     * Check if user has a permission or all functions by its name or id.
     *
     * @param  string $nameOrId Permission name or id.
     * @return bool
     */
    public function allow($nameOrId) {



        foreach ($this->roles as $role) {
            // Validate against the Permission table
            foreach ($role->permissions as $perm) {
//                        //First check to see if it's an ID
                if (is_numeric($nameOrId)) {

                    if ($perm->id == $nameOrId) {

                        return true;
                    }
                }
                //Otherwise check by name
                if (strtolower($perm->name) == strtolower($nameOrId) Or strtolower($perm->name) == "all_functions" ) {
                    return true;
                }
            }
        }

        //Check permissions directly tied to user
        foreach ($this->permissions as $perm) {
            //First check to see if it's an ID
            if (is_numeric($nameOrId)) {
                if ($perm->id == $nameOrId) {

                    return true;
                }
            }

            //Otherwise check by name
            if (strtolower($perm->name) == strtolower($nameOrId)  Or strtolower($perm->name) == "all_functions" ) {
                return true;
            }
        }


        return false;
    }

    /**
     * Check if user has a only permission by its name or id.
     *
     * @param  string $nameOrId Permission name or id.
     * @return bool
     */
    public function allowOnly($nameOrId) {



        foreach ($this->roles as $role) {

            // Validate against the Permission table
            foreach ($role->permissions as $perm) {

                //First check to see if it's an ID
                if (is_numeric($nameOrId)) {
                    if ($perm->permission_id == $nameOrId) {
                        return true;
                    }
                }

                //Otherwise check by name
                if (strtolower($perm->name) == strtolower($nameOrId)) {
                    return true;
                }
            }
        }

        //Check permissions directly tied to user
        foreach ($this->permissions as $perm) {

            //First check to see if it's an ID
            if (is_numeric($nameOrId)) {
                if ($perm->permission_id == $nameOrId) {
                    return true;
                }
            }

            //Otherwise check by name
            if ($perm->name == $nameOrId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check an array of permissions and whether or not all are required to continue
     *
     * @param  $permissions
     * @param  $needsAll
     * @return bool
     */
    public function allowMultiple($permissions, $needsAll = false) {
        //User has to possess all of the permissions specified
        if ($needsAll) {
            $hasPermissions = 0;
            $numPermissions = count($permissions);

            foreach ($permissions as $perm) {
                if ($this->allow($perm)) {
                    $hasPermissions++;
                }
            }

            return $numPermissions == $hasPermissions;
        }

        //User has to possess one of the permissions specified
        $hasPermissions = 0;
        foreach ($permissions as $perm) {
            if ($this->allow($perm)) {
                $hasPermissions++;
            }
        }

        return $hasPermissions > 0;
    }

    /**
     * @param  $nameOrId
     * @return bool
     */
    public function hasPermission($nameOrId) {
        return $this->allow($nameOrId);
    }

    /**
     * @param  $permissions
     * @param  bool           $needsAll
     * @return bool
     */
    public function hasPermissions($permissions, $needsAll = false) {
        return $this->allowMultiple($permissions, $needsAll);
    }

    /**
     * Alias to eloquent many-to-many relation's attach() method.
     *
     * @param  mixed  $role
     * @return void
     */
    public function attachRole($role) {
        if (is_object($role)) {
            $role = $role->getKey();
        }

        if (is_array($role)) {
            $role = $role['id'];
        }

        $this->roles()->attach($role);
    }

    /**
     * Alias to eloquent many-to-many relation's detach() method.
     *
     * @param  mixed  $role
     * @return void
     */
    public function detachRole($role) {
        if (is_object($role)) {
            $role = $role->getKey();
        }

        if (is_array($role)) {
            $role = $role['id'];
        }

        $this->roles()->detach($role);
    }

    /**
     * Attach multiple roles to a user
     *
     * @param  mixed  $roles
     * @return void
     */
    public function attachRoles($roles) {
        foreach ($roles as $role) {
            $this->attachRole($role);
        }
    }

    /**
     * Detach multiple roles from a user
     *
     * @param  mixed  $roles
     * @return void
     */
    public function detachRoles($roles) {
        foreach ($roles as $role) {
            $this->detachRole($role);
        }
    }

    /**
     * Attach one permission not associated with a role directly to a user
     *
     * @param $permission
     */
    public function attachPermission($permission) {
        if (is_object($permission)) {
            $permission = $permission->getKey();
        }

        if (is_array($permission)) {
            $permission = $permission['id'];
        }

        $this->permissions()->attach($permission);
    }

    /**
     * Attach other permissions not associated with a role directly to a user
     *
     * @param $permissions
     */
    public function attachPermissions($permissions) {
        if (count($permissions)) {
            foreach ($permissions as $perm) {
                $this->attachPermission($perm);
            }
        }
    }

    /**
     * Detach one permission not associated with a role directly to a user
     *
     * @param $permission
     */
    public function detachPermission($permission) {
        if (is_object($permission)) {
            $permission = $permission->getKey();
        }

        if (is_array($permission)) {
            $permission = $permission['id'];
        }

        $this->permissions()->detach($permission);
    }

    /**
     * Detach other permissions not associated with a role directly to a user
     *
     * @param $permissions
     */
    public function detachPermissions($permissions) {
        foreach ($permissions as $perm) {
            $this->detachPermission($perm);
        }
    }
}
