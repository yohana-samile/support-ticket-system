<?php

namespace App\Repositories\Backend;

use App\Models\Access\Permission;
use App\Models\Access\Role;
use App\Models\Access\User;
use App\Models\Topic;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRepository extends BaseRepository
{
    const MODEL = User::class;

    public function store(array $data)
    {
        $rawPassword = app(ClientRepository::class)->generatePassword();

        $user = DB::transaction(function() use($data, $rawPassword) {
            $user = $this->createNewUser($data, $rawPassword);
            $this->assignRolesAndPermissions($user, $data['role_id']);
            $this->assignTopicsOfSpecialization($user, $data['topic_ids'] ?? []);
            return $user;
        });
        app(ClientRepository::class)->sendEmailWithPassword($user, $user->password);
        return $user;
    }

    protected function createNewUser(array $data, string $rawPassword)
    {
        return $this->query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $rawPassword,
            'phone' => $data['phone'] ?? null,
            'department' => $data['department'] ?? null,
            'favorite_count' => 0,
            'is_active' => $data['is_active'] ?? true,
            'is_super_admin' => $data['is_super_admin'] ?? false,
            'email_verified_at' => now(),
        ]);
    }

    protected function assignTopicsOfSpecialization(User $user, array $topicIds): void
    {
        if (array('all', $topicIds)) {
            $topicIds = Topic::pluck('id')->toArray();
        }
        $user->topics()->sync($topicIds);
    }

    protected function assignRolesAndPermissions(User $user, array $roleIds): bool
    {
        $user->roles()->sync($roleIds);
        $permissionIds = Role::whereIn('id', $roleIds)->with('permissions')->get()
            ->pluck('permissions.*.id')
            ->flatten()
            ->unique()->filter()->toArray();
        $user->permissions()->sync($permissionIds);
        return true;
    }

    public function update($user, array $data)
    {
        return DB::transaction(function() use($user, $data) {
            $user->name = $data['name'] ?? $user->name;
            $user->department = $data['department'] ?? $user->department;
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

            if (!empty($data['topic_ids'])) {
                $this->assignTopicsOfSpecialization($user, $data['topic_ids']);
            }

            $user->save();
            return $user->fresh();
        });
    }

    public function delete($user)
    {
        return DB::transaction(function () use ($user) {
            $this->renamingSoftDelete($user, 'email');
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties(['user_id' => $user->id])
                ->log('deleted user');

            return $user->delete();
        });
    }

    public function updatePassword($input){
        $user = User::getUserIdByEmail($input['email']);
        $this->passwordUpdateUtil($user, $input['password']);
        return $user;
    }

    public function resendPassword($input){
        $user = User::getUserIdByEmail($input['email']);
        $newPassword = app(ClientRepository::class)->generatePassword();
        $this->passwordUpdateUtil($user, $newPassword);
        return $user;
    }

    protected function passwordUpdateUtil($user, $password)
    {
        $user->update(['password' => $password]);
        app(ClientRepository::class)->sendEmailWithPassword($user, $password);
    }

    public function getActiveManagers()
    {
        return $this->query()->where('is_active', true)->orderBy('created_at')->get();
    }

    public function getActiveForSticker()
    {
        return $this->query()->where('is_active', true)->where('id', '!=', user_id())->orderBy('created_at')->get();
    }

    public function getStaffBySpecialization(array $specializationTopics)
    {
        return $this->query()->where('is_active', true)
                ->whereHas('topics', function($q) use ($specializationTopics) {
                    $q->whereIn('id', $specializationTopics);
                })->orderBy('favorite_count', 'desc')->get();
    }

    public function getAll()
    {
        return $this->query()->withCount('tickets')->latest()->get();
    }

    public function findByUid(string $userUid)
    {
        return $this->query()->where('uid', $userUid)->first();
    }

    public function incrementFavoriteCount(User $user): User
    {
        $user->increment('favorite_count');
        return $user->fresh();
    }

    public function getAdminUsers()
    {
        return $this->query()->where('admin_id', user_id())->orderBy('created_at', 'desc')->get();
    }
}
