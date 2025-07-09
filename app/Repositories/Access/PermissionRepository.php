<?php


namespace App\Repositories\Access;



use App\Models\Access\Permission;
use App\Models\Access\Role;
use App\Models\Access\User;
use App\Repositories\BaseRepository;

class PermissionRepository extends BaseRepository
{

    const  MODEL = Permission::class;

    /*Get all permissions*/
    public  function  getAll() {
        return $this->query()->with('permissionGroup')->orderBy('display_name')->get()->groupBy('permission_group_id');
    }

    /*Get all permissions which are non-administrative*/
    public function getAllNonAdministrative() {
        return $this->query()->whereNull('deleted_at')->where('isadmin', 0)->orderBy('display_name')->get();
    }

    public function getPermissionsByRole(Role $role) {
        return $role->permissions()->with('permissionGroup')->orderBy('display_name')->get()->groupBy('permission_group_id');
    }

    /*Check if permission is in user roles*/
    public function checkIfPermissionIsInUserRoles($user_id, $permission_id)
    {
        $user = User::query()->find($user_id);
        $check = $user->roles()->whereHas('permissions', function($query) use($permission_id){
            $query->where('permissions.id', $permission_id);
        })->count();
        if($check > 0)
        {
            return true;
        }else{
            return false;
        }
    }
}
