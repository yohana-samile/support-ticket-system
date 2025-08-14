<?php

use Illuminate\Database\Seeder;
use Database\TruncateTable;
use Database\DisableForeignKeys;
use Illuminate\Support\Str;
use App\Models\Access\PermissionGroup;

class PermissionGroupTableSeeder extends Seeder
{

    use DisableForeignKeys, TruncateTable;
    public function run()
    {

        $this->disableForeignKeys('permission_groups');
        $this->delete('permission_groups');

        $permissionGroups = [
            ['name' => 'Administration', 'description' => 'System administration permissions'],
            ['name' => 'Client Management', 'description' => 'Permissions for managing client accounts'],
            ['name' => 'Staff Management', 'description' => 'Permissions for managing staff accounts'],
            ['name' => 'Report Management', 'description' => 'Permissions for generating and viewing reports'],
            ['name' => 'General Setting Management', 'description' => 'Permissions for system general settings'],
            ['name' => 'Other Setting Management', 'description' => 'Permissions for other system settings'],
            ['name' => 'Ticket Management', 'description' => 'Permissions for ticket operations']
        ];

        foreach ($permissionGroups as $group) {
            PermissionGroup::updateOrCreate(
                ['name' => $group['name']],
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
