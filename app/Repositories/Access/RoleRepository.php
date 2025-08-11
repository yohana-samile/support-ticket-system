<?php

namespace App\Repositories\Access;



use App\Models\Access\Role;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RoleRepository extends BaseRepository
{
    const MODEL = Role::class;

    public function  getDetail($id){

        return $this->query()->where('id', $id)->first();
    }

    public function  getActiveRoles(){

        return $this->query()->where('isactive', true)->get();
    }

    public function forSelect()
    {
        return $this->query()->where('isactive', 1)->pluck('name', 'id');
    }

    public function getNonAdministrativeRolesForSelect()
    {
        return $this->query()->select(['id', 'name'])->where("isadmin", 0)->orderBy("id", "asc")->get()->pluck("name", "id");;
    }

    /*Get Administrative roles for select*/
    public function getAdministrativeRolesForSelect()
    {
        return $this->query()->select(['id', 'name'])->where("isadmin", 1)->orderBy("id", "asc")->get()->pluck("name", "id");;
    }


    public function getAllForDt()
    {
        return $this->query()->withCount('users')->get();
    }


    public function store(array $input)
    {
        return DB::transaction(function () use ($input) {
            $role = $this->query()->create([
                'id' => $this->getNextId(),
                'name' => $input['name'],
                'description' => $input['description'],
                'isadmin' => isset($input['isadmin']) && $input['isadmin'] === 'on' ? 1 : 0,
                'isactive' => isset($input['isactive']) && $input['isactive'] === 'on' ? 1 : 0,
            ]);
            return $role;
        });
    }


    /*Update Role and Permissions */
    public function update(array $input, Model $role)
    {
        return  DB :: transaction(function() use ($input, $role){
            $this->updateRole($input, $role);
            $this->updateRolePermissions($input, $role);
            return $role;
        });
    }

    /*Update role info to Role table*/
    public function updateRole(array $input, Model $role)
    {
        return  DB :: transaction(function() use ($input, $role){
            $role->update([
                'name' => $input['name'],
                'description' => $input['description'],
                'isadmin' => $input['isadmin'] ?? 0,
                'isactive' => $input['isactive'] ?? 0,
            ]);
            return $role;
        });
    }


    /*Update sync permissions with role*/
    public function updateRolePermissions(array $input, Model $role)
    {
        return DB::transaction(function () use ($input, $role) {
            if (isset($input['permissions']) && is_array($input['permissions'])) {
                $role->permissions()->sync($input['permissions']);
            }
            else {
                $role->permissions()->detach();
            }
            return $role;
        });
    }

    public function delete(Model $role)
    {
        $role->permissions()->sync([]);
        $this->renamingSoftDelete($role, 'name');
        $role->delete();
        return true;
    }

    /*Get The max id*/
    public function getMaxId()
    {
        return $this->query()->max('id');
    }

    /*Get the next id to be used on the new entry*/
    public function getNextId()
    {
        return $this->getMaxId() + 1;
    }
}
