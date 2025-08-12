<?php

namespace Database\Seeders;

use App\Models\Landlord\Permission;
use App\Models\Landlord\Role;
use App\Models\Landlord\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RootSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rootUsers = [
            [
                'name' => 'Lucas Negrello',
                'email' => 'lucas123kaas@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => 'lucas123kaas@gmail.com@123',
                'roles' => ['root'],
                'permissions' => []
            ],
            [
                'name' => 'Root Admin',
                'email' => 'root_admin@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => 'root_admin@gmail.com@123',
                'roles' => ['root_admin'],
                'permissions' => []
            ],
            [
                'name' => 'Root Manager',
                'email' => 'root_manager@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => 'root_manager@gmail.com@123',
                'roles' => ['root_manager'],
                'permissions' => []
            ],
            [
                'name' => 'Root User',
                'email' => 'root_user@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => 'root_user@gmail.com@123',
                'roles' => ['root_user'],
                'permissions' => []
            ],
        ];

        $tenantUsers = [
            [
                'name' => 'Tenant Admin',
                'email' => 'admin@tenant.com',
                'email_verified_at' => Carbon::now(),
                'password' => 'password',
                'roles' => ['admin'],
                'permissions' => []
            ],
            [
                'name' => 'Tenant Manager',
                'email' => 'manager@tenant.com',
                'email_verified_at' => Carbon::now(),
                'password' => 'password',
                'roles' => ['manager'],
                'permissions' => []
            ],
            [
                'name' => 'Tenant User',
                'email' => 'user@tenant.com',
                'email_verified_at' => Carbon::now(),
                'password' => 'password',
                'roles' => ['user'],
                'permissions' => []
            ],
            [
                'name' => 'Tenant Guest',
                'email' => 'guest@tenant.com',
                'email_verified_at' => Carbon::now(),
                'password' => 'password',
                'roles' => ['guest'],
                'permissions' => []
            ],
        ];

        $users = array_merge($rootUsers, $tenantUsers);

        foreach ($users as $userData) {
            $userModel = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'status' => User::ACTIVE,
                    'email_verified_at' => $userData['email_verified_at'],
                    'password' => $userData['password'],
                ]
            );
            if (!empty($userData['roles'])) {
                foreach ($userData['roles'] as $roleName) {
                    $roleModel = Role::where('name', $roleName)->first();
                    if ($roleModel) {
                        $userModel->assignRole($roleModel->name);
                    }
                }
            }
            if (!empty($userData['permissions'])) {
                $permissionsIds = [];
                foreach ($userData['permissions'] as $permission => $actions) {
                    foreach ($actions as $action) {
                        $permissionModel = Permission::where('name', "{$permission}_{$action}")->first();
                        if ($permissionModel) {
                            $permissionsIds[] = $permissionModel->getKey();
                        }
                    }
                }
                if (!empty($permissionsIds)) {
                    $userModel->permissions()->syncWithoutDetaching($permissionsIds);
                }
            }
        }
    }
}
