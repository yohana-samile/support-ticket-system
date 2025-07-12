<?php

use App\Models\Access\Permission;
use Illuminate\Database\Seeder;
use Database\TruncateTable;
use Database\DisableForeignKeys;
use Illuminate\Support\Str;

class PermissionsTableSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;
    /**
     * Auto generated seed file
     *
     * @return void
     * @return void
     */
    public function run()
    {
//        Permission::truncate();
        $this->disableForeignKeys('permissions');
        $this->delete('permissions');
        $this->alterIdSequence('permissions');

        $permissions = [
            ['name' => 'all_functions', 'display_name' => 'All Functions', 'permission_group_id' => 1, 'isadmin' => 1],
            ['name' => 'client', 'display_name' => 'Client', 'permission_group_id' => 2, 'isadmin' => 1],
            ['name' => 'customer_supporter', 'display_name' => 'Customer Supporter', 'permission_group_id' => 2, 'isadmin' => 1],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                array_merge($permission, [
                    'description' => $permission['display_name'] . ' permission',
                    'ischecker' => 0,
                    'isactive' => 1,
                    'guard_name' => 'api',
                    'uid' => Str::uuid(),
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        $this->enableForeignKeys('permissions');
    }
}
