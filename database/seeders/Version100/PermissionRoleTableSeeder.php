<?php

use App\Models\Access\Permission;
use App\Models\Access\Role;
use Illuminate\Database\Seeder;
use Database\TruncateTable;
use Database\DisableForeignKeys;
use Illuminate\Support\Facades\DB;

class PermissionRoleTableSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    public function run()
    {
        $this->disableForeignKeys("permission_role");
        $this->delete('permission_role');

        $this->assignPermissionsToRole('administration', Permission::query()->pluck('id')->toArray());
        $this->assignPermissionsToRole('support_agent', [
            'view_tickets',
            'create_tickets',
            'edit_tickets',
            'assign_tickets',
            'resolve_tickets',
            'view_reports'
        ]);
        $this->assignPermissionsToRole('report_viewer', [
            'view_reports'
        ]);
        $this->assignPermissionsToRole('client', [
            'view_tickets',
            'create_tickets'
        ]);

        $this->enableForeignKeys('permission_role');
    }

    protected function assignPermissionsToRole($roleName, $permissionNames): void
    {
        $roleId = Role::query()->where('name', $roleName)->value('id');
        if (!$roleId) return;

        if (is_array($permissionNames)) {
            $permissionIds = ctype_digit(implode('', $permissionNames)) || is_int($permissionNames[0])
                ? $permissionNames
                : DB::table('permissions')
                    ->whereIn('name', $permissionNames)
                    ->pluck('id')
                    ->toArray();
        } else {
            $permissionIds = is_numeric($permissionNames)
                ? [$permissionNames]
                : DB::table('permissions')
                    ->where('name', $permissionNames)
                    ->pluck('id')
                    ->toArray();
        }

        $insertData = array_map(function($permissionId) use ($roleId) {
            return [
                'permission_id' => $permissionId,
                'role_id' => $roleId,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }, $permissionIds);

        DB::table('permission_role')->insert($insertData);
    }
}
