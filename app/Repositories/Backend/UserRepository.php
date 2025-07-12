<?php

namespace App\Repositories\Backend;

use App\Models\Access\Permission;
use App\Models\Access\Role;
use App\Models\Access\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRepository extends BaseRepository
{
    const MODEL = User::class;

    public function store(array $data)
    {
        return DB::transaction(function() use($data) {
            $user = $this->createNewUser($data);
            if ($user->wasRecentlyCreated) {
                $this->assignRolesAndPermissions($user, $data['role_id']);
            }
            return $user;
        });
    }

    protected function createNewUser(array $data)
    {
        $role = Role::getRoleById($data['role_id']);
        $reporter = $role && $role->name === 'reporter';

        return $this->query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'department' => $data['department'] ?? null,
            'specializations' => $data['specializations'] ?? [],
            'favorite_count' => 0,
            'is_active' => $data['is_active'] ?? true,
            'is_super_admin' => $data['is_super_admin'] ?? false,
            'is_reporter' => $reporter,
            'email_verified_at' => now(),
            'uid' => Str::uuid()
        ]);
    }

    protected function assignRolesAndPermissions(User $user, int $roleId): bool
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

    public function update($userId, array $data)
    {
        return DB::transaction(function() use($userId, $data) {
            $user = is_numeric($userId)
                ? User::getUserIdById($userId)
                : User::getUserIdByUid($userId);

            if (!$user) {
                return false;
            }

            $user->name = $data['name'] ?? $user->name;
            $user->department = $data['department'] ?? $user->department;
            $user->specializations = $data['specializations'] ?? $user->specializations;
            $user->is_active = ($data['is_active'] ?? $user->is_active) ? 1 : 0;

            if (!empty($data['email']) && $data['email'] !== $user->email) {
                $user->email = $data['email'];
                $user->email_verified_at = null;
            }

            if (!empty($data['password'])) {
                $user->password = $data['password'];
            }

            if (!empty($data['roles'])) {
                $roleIds = Role::query()
                    ->whereIn('name', (array) $data['roles'])
                    ->pluck('id')
                    ->toArray();
                $user->roles()->sync($roleIds);
            }

            if (!empty($data['permissions'])) {
                $permissionIds = Permission::query()
                    ->whereIn('name', (array) $data['permissions'])
                    ->pluck('id')
                    ->toArray();
                $user->permissions()->sync($permissionIds);
            }

            $user->save();
            return $user->fresh();
        });
    }

    public function delete($userId)
    {
        return DB::transaction(function () use ($userId) {
            $user = is_numeric($userId)
                ? User::getUserIdById($userId)
                : User::getUserIdByUid($userId);

            if (!$user) {
                return false;
            }

            $this->renamingSoftDelete($user, 'email');

            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties(['user_id' => $user->id])
                ->log('deleted user');

            return $user->delete();
        });
    }

    public function getActiveManagers()
    {
        return $this->query()->where('is_active', true)->where('is_super_admin', true)->orderBy('created_at')->get();
    }

    public function getManagersBySpecialization(array $specializations)
    {
        return $this->query()->where('is_active', true)
            ->whereHas('roles', function($q) {
                $q->where('name', 'administration');
            })
            ->whereJsonContains('specializations', $specializations)->orderBy('favorite_count', 'desc')->get();
    }

    public function getAll()
    {
        return $this->query()->withCount('tickets')->latest()->get();
    }

    public function findByUid(string $userUid)
    {
        return $this->query()->where('uid', $userUid)->first();
    }

    public function incrementFavoriteCount(User $manager): User
    {
        $manager->increment('favorite_count');
        return $manager->fresh();
    }

    public function getAdminUsers()
    {
        return $this->query()->where('admin_id', auth()->id())->orderBy('created_at', 'desc')->get();
    }
}
