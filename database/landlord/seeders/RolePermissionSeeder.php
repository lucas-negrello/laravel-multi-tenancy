<?php

namespace Database\Seeders;

use App\Models\Landlord\Permission;
use App\Models\Landlord\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'root',          'description' => 'Root role with all permissions',                  'is_tenant_base' => false],
            ['name' => 'root_admin',    'description' => 'Root admin role with elevated permissions',       'is_tenant_base' => false],
            ['name' => 'root_manager',  'description' => 'Root manager role with management permissions',   'is_tenant_base' => false],
            ['name' => 'root_user',     'description' => 'Root user role with basic permissions',           'is_tenant_base' => false],
            ['name' => 'admin',         'description' => 'Admin role with administrative permissions',      'is_tenant_base' => true],
            ['name' => 'manager',       'description' => 'Manager role with management permissions',        'is_tenant_base' => true],
            ['name' => 'user',          'description' => 'User role with basic permissions',                'is_tenant_base' => true],
            ['name' => 'guest',         'description' => 'User role with limited permissions',              'is_tenant_base' => true],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }

        $landlordResources = [
            'users'             => 'Users',
            'roles'             => 'Roles',
            'permissions'       => 'Permissions',
            'tenants'           => 'Tenants',
        ];

        $tenantResources = [
            'spaces'            => 'Spaces',
        ];

        $resources = array_merge($landlordResources, $tenantResources);

        $actions = [
            'view'      => 'View',
            'create'    => 'Create',
            'update'    => 'Update',
            'delete'    => 'Delete',
        ];

        $permissionsArray = [];
        $permissionsArray[] = [
            'name' => 'full_access',
            'description' => 'Full access to all resources',
        ];

        foreach ($resources as $resourceKey => $resourceName) {
            foreach (array_keys($actions) as $action) {
                $permissionsArray[] = [
                    'name' => "{$resourceKey}_{$action}",
                    'description' => "{$actions[$action]} {$resourceName}",
                ];
            }
        }

        foreach ($permissionsArray as $permissionData) {
            Permission::updateOrCreate(['name' => $permissionData['name']], $permissionData);
        }

        $landlordRoleAllowedActions = [
            'root' => 'all',
            'root_admin' => [
                'users' => ['view', 'create', 'update', 'delete'],
                'roles' => ['view', 'create', 'update', 'delete'],
                'permissions' => ['view', 'create', 'update', 'delete'],
                'tenants' => ['view', 'create', 'update', 'delete'],
                'spaces' => ['view', 'create', 'update', 'delete'],
            ],
            'root_manager' => [
                'users' => ['view', 'create', 'update'],
                'roles' => ['view', 'update'],
                'permissions' => ['view', 'update'],
                'tenants' => ['view', 'create', 'update'],
                'spaces' => ['view', 'create', 'update'],
            ],
            'root_user' => [
                'users' => ['view', 'update'],
                'roles' => ['view'],
                'permissions' => ['view'],
                'tenants' => ['view'],
                'spaces' => ['view'],
            ],
        ];

        $tenantRoleAllowedActions = [
            'admin' => [
                'users' => ['view', 'create', 'update', 'delete'],
                'roles' => ['view', 'create', 'update', 'delete'],
                'permissions' => ['view', 'create', 'update'],
                'tenants' => ['view', 'create', 'update'],
                'spaces' => ['view', 'create', 'update', 'delete'],
            ],
            'manager' => [
                'users' => ['view', 'create', 'update'],
                'roles' => ['view', 'update'],
                'permissions' => ['view', 'update'],
                'tenants' => ['view'],
                'spaces' => ['view', 'update'],
            ],
            'user' => [
                'users' => ['view'],
                'roles' => ['view'],
                'permissions' => ['view'],
                'tenants' => ['view'],
                'spaces' => ['view'],
            ],
            'guest' => [
                'spaces' => ['view'],
            ],
        ];

        $roleAllowedActions = array_merge($landlordRoleAllowedActions, $tenantRoleAllowedActions);

        $rolePermissionsMap = [];

        foreach ($roleAllowedActions as $roleName => $allowed) {
            if ($allowed === 'all')
                $rolePermissionsMap[$roleName] = Permission::pluck('id')->toArray();
            else {
                $names = [];
                foreach ($allowed as $resourceKey => $allowedActions) {
                    foreach ($allowedActions as $action) {
                        $names[] = "{$resourceKey}_{$action}";
                    }
                }
                $rolePermissionsMap[$roleName] = Permission::whereIn('name', $names)->pluck('id')->toArray();
            }

        }

        foreach ($rolePermissionsMap as $roleName => $permissionIds) {
            $role = Role::where('name', $roleName)->first();
            if ($role)
                $role->permissions()->sync($permissionIds);
        }
   }
}
