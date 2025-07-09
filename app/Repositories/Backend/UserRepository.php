<?php

namespace App\Repositories\Backend;
use App\Models\Access\Permission;
use App\Models\Access\Role;
use App\Models\Access\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class UserRepository extends  BaseRepository {
    const MODEL = User::class;
    public function store(array $input) {
        return DB::transaction(function() use($input) {
            $user = $this->createNewUser($input);
            if ($user->wasRecentlyCreated) {
                $this->assignRolesAndPermissions($user, $input['role_id']);
            }
            return $user;
        });
    }

    protected function createNewUser(array $input)
    {
        $role = Role::getRoleById($input['role_id']);
        $reporter = $role && $role->name === 'reporter';

        return $this->query()->create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'is_active' => $input['is_active'] ?? true,
            'is_super_admin' => $input['is_super_admin'] ?? false,
            'is_reporter' => $reporter,
            "email_verified_at" => now(),
        ]);
    }

    protected function assignRolesAndPermissions(User $user, int $roleId):bool
    {
        $role = Role::getRoleById($roleId);
        if (!$role) {
            throw new \InvalidArgumentException("Role with ID {$roleId} not found");
        }
        $user->roles()->sync([$role->id]);
        if ($role->permissions->isNotEmpty()) {
            $user->permissions()->sync($role->permissions->pluck('id')->toArray());
        }
        return true;
    }

    public function update($userId, array $input){
        if (is_numeric($userId)) {
            $user = User::getUserIdById($userId);
        } else {
            $user = User::getUserIdByUid($userId);
        }
        if(!$user) {
            return false;
        }
        $user->name = $input['name'];
        $user->is_active = ($input['is_active'] ?? false) === 'on' || ($input['is_active'] ?? false) == 1 ? 1 : 0;

        if (!empty($input['email']) && $input['email'] !== $user->email) {
            $user->email = $input['email'];
            $user->email_verified_at = null;
        }

        if (!empty($input['password'])) {
            $user->password = $input['password'];
        }
        if (!empty($input['roles'])) {
            $roleIds = Role::query()->whereIn('name', (array) $input['roles'])->pluck('id')->toArray();
            $user->roles()->sync($roleIds);
        }

        if (!empty($input['permissions'])) {
            $permissionIds = Permission::query()->whereIn('name', (array) $input['permissions'])->pluck('id')->toArray();
            $user->permissions()->sync($permissionIds);
        }

        $user->save();
        return true;
    }

    public function delete($userId) {
        if (is_numeric($userId)) {
            $user = User::getUserIdById($userId);
        } else {
            $user = User::getUserIdByUid($userId);
        }
        if(!$user) {
            return false;
        }
        $this->renamingSoftDelete($user, 'email');
        $user->delete();
        return true;
    }

    public function getAllForDt() {
        return $this->query()->orderBy('created_at', 'desc')->get();
    }

    public function fetchUserByUid($userUid) {
        return $this->query()->where('uid', $userUid)->first();
    }

    public function getAdminUsers() {
        return $this->query()->where('admin_id', user_id())->orderBy('created_at', 'desc')->get();
    }
}
