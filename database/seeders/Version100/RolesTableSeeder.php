<?php

use App\Models\Access\Role;
use Illuminate\Database\Seeder;
use Database\TruncateTable;
use Database\DisableForeignKeys;
use Illuminate\Support\Str;

class RolesTableSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run() {
        $exists = Role::query()->count();
        if($exists == 0){
            $roles = [
                ['name' => 'administration', 'display_name' => 'Administration', 'description' => 'Administrative access', 'isadmin' => 1],
                ['name' => 'customer_supporter', 'display_name' => 'Customer Supporter', 'description' => 'customer supporter', 'isadmin' => 1],
                ['name' => 'client', 'display_name' => 'Client', 'description' => 'report any issue', 'isadmin' => 0],
            ];

            foreach ($roles as $role) {
                Role::updateOrCreate(
                    ['name' => $role['name']],
                    array_merge($role, [
                        'isactive' => 1,
                        'guard_name' => 'api',
                        'uid' => Str::uuid(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
            }
        }
        $this->alterIdSequence('roles');
    }
}
