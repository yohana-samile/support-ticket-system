<?php

use App\Models\Access\Permission;
use App\Models\Access\PermissionGroup;
use Illuminate\Database\Seeder;
use Database\TruncateTable;
use Database\DisableForeignKeys;
use Illuminate\Support\Str;

class PermissionsTableSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    public function run()
    {
//        Permission::truncate();
        $this->disableForeignKeys('permissions');
        $this->delete('permissions');
        $this->alterIdSequence('permissions');

        $groupNames = [
            'Administration',
            'Client Management',
            'Staff Management',
            'Report Management',
            'General Setting Management',
            'Other Setting Management',
            'Ticket Management'
        ];
        $groups = PermissionGroup::whereIn('name', $groupNames)->pluck('id', 'name')->toArray();

        $permissions = [
            [
                'group_name' => 'Administration',
                'permissions' => [
                    ['name' => 'all_functions', 'display_name' => 'All Functions', 'isadmin' => 1],
                    ['name' => 'manage_roles_permissions', 'display_name' => 'Manage Roles & Permissions', 'isadmin' => 1],
                ]
            ],
            [
                'group_name' => 'Client Management',
                'permissions' => [
                    ['name' => 'view_clients', 'display_name' => 'View Clients'],
                    ['name' => 'create_clients', 'display_name' => 'Create Clients'],
                    ['name' => 'edit_clients', 'display_name' => 'Edit Clients'],
                    ['name' => 'delete_clients', 'display_name' => 'Delete Clients'],
                ]
            ],
            [
                'group_name' => 'Staff Management',
                'permissions' => [
                    ['name' => 'view_staff', 'display_name' => 'View Staff'],
                    ['name' => 'create_staff', 'display_name' => 'Create Staff'],
                    ['name' => 'edit_staff', 'display_name' => 'Edit Staff'],
                    ['name' => 'delete_staff', 'display_name' => 'Delete Staff'],
                    ['name' => 'manage_roles', 'display_name' => 'Manage Roles'],
                    ['name' => 'manage_permissions', 'display_name' => 'Manage Permissions'],
                ]
            ],
            [
                'group_name' => 'Report Management',
                'permissions' => [
                    ['name' => 'view_reports', 'display_name' => 'View Reports'],
                    ['name' => 'generate_reports', 'display_name' => 'Generate Reports'],
                    ['name' => 'export_reports', 'display_name' => 'Export Reports'],
                ]
            ],
            [
                'group_name' => 'General Setting Management',
                'permissions' => [
                    ['name' => 'manage_topics', 'display_name' => 'Manage Topics'],
                    ['name' => 'manage_subtopics', 'display_name' => 'Manage Subtopics'],
                    ['name' => 'manage_tertiary_topics', 'display_name' => 'Manage Tertiary Topics'],
                ]
            ],
            [
                'group_name' => 'Other Setting Management',
                'permissions' => [
                    ['name' => 'manage_operators', 'display_name' => 'Manage MNOs'],
                    ['name' => 'manage_saas_apps', 'display_name' => 'Manage SaaS Apps'],
                    ['name' => 'manage_sender_ids', 'display_name' => 'Manage Sender IDs'],
                ]
            ],
            [
                'group_name' => 'Ticket Management',
                'permissions' => [
                    ['name' => 'view_tickets', 'display_name' => 'View Tickets'],
                    ['name' => 'create_tickets', 'display_name' => 'Create Tickets'],
                    ['name' => 'edit_tickets', 'display_name' => 'Edit Tickets'],
                    ['name' => 'delete_tickets', 'display_name' => 'Delete Tickets'],
                    ['name' => 'assign_tickets', 'display_name' => 'Assign Tickets'],
                    ['name' => 'resolve_tickets', 'display_name' => 'Resolve Tickets'],
                ]
            ]
        ];

        foreach ($permissions as $group) {
            if (!isset($groups[$group['group_name']])) {
                continue;
            }
            $groupId = $groups[$group['group_name']];

            foreach ($group['permissions'] as $permission) {
                Permission::updateOrCreate(
                    ['name' => $permission['name']],
                    array_merge($permission, [
                        'permission_group_id' => $groupId,
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
        }

        $this->enableForeignKeys('permissions');
    }
}
