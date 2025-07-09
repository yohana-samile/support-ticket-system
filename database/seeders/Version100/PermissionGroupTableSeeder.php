<?php

use Illuminate\Database\Seeder;
use Database\TruncateTable;
use Database\DisableForeignKeys;
use Illuminate\Support\Str;
use App\Models\Access\PermissionGroup;

class PermissionGroupTableSeeder extends Seeder
{

    use DisableForeignKeys, TruncateTable;
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        $this->disableForeignKeys('permission_groups');
        $this->delete('permission_groups');

        $permissionGroups = [
            ['id' => 1, 'name' => 'Administration', 'description' => 'admin only permissions'],
            ['id' => 3, 'name' => 'Reporter', 'description' => 'Can report any issue'],
            ['id' => 3, 'name' => 'Customer Supporter', 'description' => 'Customer Supporter'],
        ];

        foreach ($permissionGroups as $group) {
            PermissionGroup::updateOrCreate(
                ['id' => $group['id']],
                array_merge($group, [
                    'uid' => Str::uuid(),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null
                ])
            );
        }

        $this->enableForeignKeys('permission_groups');
    }
}
