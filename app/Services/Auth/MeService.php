<?php

namespace App\Services\Auth;

use App\Models\Landlord\User;
use Illuminate\Http\Request;

class MeService {
    public function getMeInfo(Request $request)
    {
        $user = $this->getUser();

        $user->loadMissing([
            'tenants',
            'roles.permissions',
            'permissions',
        ]);

        $tenants        = $this->getTenants($user);
        $tenant         = $this->getTenant($request, $user);
        $roles          = $this->getRoles($user);
        $permissions    = $this->getPermissions($user);

        $user = $user->withoutRelations();

        if ($tenant)
            $tenant = $tenant->withoutRelations();

        return [
            'user' => $user,
            'tenants' => $tenants,
            'tenant' => $tenant,
            'roles' => $roles,
            'permissions' => $permissions,
        ];
    }

    private function getUser(): User
    {
        /**@var User $user*/
        $user = auth()->user();
        return $user;
    }

    private function getTenants(User $user)
    {
        return $user->tenants->map->withoutRelations();
    }

    private function getTenant(Request $request, User $user)
    {
        $tenantId = $request->header('X-Tenant-Identifier');

        if (!$tenantId) return null;

        return $user->tenants->firstWhere('schema', $tenantId) ?? null;
    }

    private function getRoles(User $user)
    {
        return $user->roles->map->withoutRelations();
    }

    private function getPermissions(User $user)
    {
        $permissionsFromRoles = $user->roles
            ->flatMap->permissions;

        return $user->permissions
            ->concat($permissionsFromRoles)
            ->unique('id')
            ->values()
            ->map->withoutRelations();
    }
}
