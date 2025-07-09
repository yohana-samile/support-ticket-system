<?php

namespace App\Services\Access;
use App\Models\Access\Permission;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class Access.
 */
class Access
{
    /**
     * Get the currently authenticated user or null.
     */
    public function user($guard = 'web')
    {
        return auth($guard)->user();
    }

    /**
     * Return if the current session user is a guest or not.
     *
     * @return mixed
     */
    public function guest($guard = 'web')
    {
        return auth($guard)->guest();
    }

    /**
     * @return mixed
     */
    public function logout($guard = 'web')
    {

        return auth($guard)->logout();
    }

    /**
     * Get the currently authenticated user's id.
     *
     * @return mixed
     */
    public function id($guard = 'web')
    {
        return auth($guard)->id();
    }

    /**
     * @param Authenticatable $user
     * @param bool            $remember
     */
    public function login(Authenticatable $user, $remember = false,$guard = 'web')
    {
        $logged_in = auth($guard)->login($user, $remember);
        return $logged_in;
    }

    /**
     * Check whether user is authenticated or not ...
     *
     * @return bool
     */
    public function check($guard = 'web')
    {
        return auth($guard)->check();
    }

    public function viaRemember($guard = 'web')
    {
        return auth($guard)->viaRemember();
    }


    /**
     * Return all users
     * @return array
     */
    public function allUsers()
    {
        $return = [];
        $user = $this->user();
//        if ($this->substitutingCount()) {
//            $return = $user->substitutingUsers()->select(['id'])->pluck("id")->toArray();
//        }
        $return[] = $this->id();
        return $return;
    }


    /**
     * Check if the current user has a permission by its name or id.
     *
     * @param string $permission Permission name or id.
     *
     * @return bool
     */
    public function allow($permission)
    {
        $return = false;
        if ($user = $this->user()) {
            $return = $user->allow($permission);
        }else{
            /*Allow guest*/

        }

        return $return;
    }


    /**
     * @param $permission_group_id
     * Get All Permissions
     */
    public function getAllPermissionNamesArrayByGroup(array $permission_group_ids)
    {
        $user = $this->user();
        $role_ids = $user->roles()->pluck('roles.id');

        $permissions_role = Permission::query()->select('permissions.name')->whereIn('permission_group_id', $permission_group_ids)->whereHas('roles', function($q) use($role_ids){
            $q->whereIn('roles.id', $role_ids);
        })->pluck('permissions.name', 'permissions.name')->toArray();

        $permissions_user=$user->permissions()->whereIn('permission_group_id', $permission_group_ids)->pluck('permissions.name', 'permissions.name')->toArray();;
//        dd($permissions_role);
        $permissions = array_merge($permissions_role,$permissions_user);

        return $permissions;
    }

}
