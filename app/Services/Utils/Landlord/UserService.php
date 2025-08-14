<?php

namespace App\Services\Utils\Landlord;

use App\Models\Landlord\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class UserService
{

    /**
     * Convert a User model to an array including roles, permissions, and tenants.
     */
    public static function userToArray(User $user, bool $relations = false): array
    {
        $roles = self::getRoles($user, $relations);
        $permissions = self::getPermissions($user, $relations);
        $tenants = self::getTenants($user, $relations);

        if (!$relations)
            $user = $user->withoutRelations();

        return [
            'user' => $user,
            'roles' => $roles,
            'permissions' => $permissions,
            'tenants' => $tenants,
        ];
    }

    /**
     * Convert a collection of User models to an array of arrays including roles, permissions, and tenants.
     */
    public static function usersToArray(Collection $users, bool $relations = false): array
    {
        return collect($users)->map(function ($user) use ($relations) {
            return self::userToArray($user, $relations);
        })->toArray();
    }

    /**
     * Convert the currently logged-in user to an array including roles, permissions, and tenants.
     */
    public static function loggedUserToArray(Request $request, bool $relations = false): array
    {
        $user = self::getLoggedUser();

        $formattedUser = self::userToArray($user, $relations);

        $tenant = self::getTenant($request, $user);

        if ($tenant && !$relations) {
            $tenant = $tenant->withoutRelations();
        }

        return array_merge($formattedUser, ['tenant' => $tenant]);
    }

    /**
     * Get the currently logged-in user.
     */
    public static function getLoggedUser(): User
    {
        /**@var User $user*/
        $user = auth()->user();
        return $user;
    }

    /**
     * Get the tenants associated with the user.
     */
    public static function getTenants(User $user, bool $relations = false)
    {
        if (!$relations)
            return $user->tenants->map->withoutRelations();
        return $user->tenants;
    }

    /**
     * Get the tenant based on the 'X-Tenant-Identifier' header from the request.
     */
    public static function getTenant(Request $request, User $user)
    {
        $tenantId = $request->header('X-Tenant-Identifier');

        if (!$tenantId) return null;

        return $user->tenants->firstWhere('schema', $tenantId) ?? null;
    }

    /**
     * Get the roles associated with the user.
     */
    public static function getRoles(User $user, bool $relations = false)
    {
        if (!$relations)
            return $user->roles->map->withoutRelations();
        return $user->roles;
    }

    /**
     * Get the permissions associated with the user, including those from roles.
     */
    public static function getPermissions(User $user, bool $relations = false)
    {
        $permissionsFromRoles = $user->roles
            ->flatMap->permissions;

        if (!$relations)
            return $user->permissions
                ->concat($permissionsFromRoles)
                ->unique('id')
                ->values()
                ->map->withoutRelations();

        return $user->permissions
            ->concat($permissionsFromRoles)
            ->unique('id')
            ->values();
    }

}
