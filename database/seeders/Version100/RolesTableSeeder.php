<?php

use App\Models\Access\Role;
use Illuminate\Database\Seeder;
use Database\TruncateTable;
use Database\DisableForeignKeys;
use Illuminate\Support\Str;

class RolesTableSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    public function run() {
        $roles = [
            ['name' => 'administration', 'display_name' => 'Administration', 'description' => 'Administrative access', 'isadmin' => 1],
            ['name' => 'security_admin', 'display_name' => 'Security Administrator', 'description' => 'Manages roles and permissions', 'isadmin' => 1],
            ['name' => 'support_agent', 'display_name' => 'Support Agent', 'description' => 'Can manage tickets and view reports', 'isadmin' => 1],
            ['name' => 'client', 'display_name' => 'Client', 'description' => 'Can create and view their own tickets', 'isadmin' => 1],
            ['name' => 'report_viewer', 'display_name' => 'Report Viewer', 'description' => 'Can only view reports', 'isadmin' => 1]
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
        $this->alterIdSequence('roles');
    }
}
