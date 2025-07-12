<?php

use App\Models\Access\Permission;
use App\Models\Access\Role;
use App\Models\Access\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
    {
        use \Database\DisableForeignKeys, \Database\TruncateTable;
        public function run(): void
        {
            $this->disableForeignKeys("users");
            $userRepo = new \App\Repositories\Access\UserRepository();
            $count = $userRepo->query()->count();
            $email = 'admin@ticketing.co.tz';
            $password = 12345678;

            if($count == 0) {
                /* 1 create admin user */
                $user = $userRepo->query()->updateOrCreate(['email' => $email],[
                    "name" => "Admin User",
                    "is_active" => true,
                    "is_super_admin" => true,
                    "email_verified_at" => now(),
                    "email" => $email,
                    "phone" => '+255620350083',
                    "password" => Hash::make($password),
                ]);

                /* 3. Assign role to user (add entry to role_user table) */
                $role = Role::getRoleByName('Administration');
                if ($role) {
                    $user->roles()->sync([$role->id]);
                }
                $permission = Permission::getPermissionByName('all_functions');
                if ($permission) {
                    $user->permissions()->sync([$permission->id]);
                }
            }
            else{
                /* 3. Assign role to user (add entry to role_user table) */
                $user = User::getUserIdByEmail($email);
                $role = Role::getRoleByName('Administration');
                $permission = Permission::getPermissionByName('all_functions');

                if ($role) {
                    $user->roles()->sync([$role->id]);
                }
                if ($permission) {
                    $user->permissions()->sync([$permission->id]);
                }
            }
            $this->enableForeignKeys("users");
        }
    }
