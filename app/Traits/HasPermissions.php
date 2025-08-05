<?php

namespace App\Traits;

use App\Models\Landlord\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermissions
{
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'users_roles_permissions',
            'user_id',
            'permission_id'
        );
    }

    public function hasExplicitPermission(int|array|string $permission): bool
    {
        if (is_string($permission)) {
            return $this->permissions()->where('name', $permission)->exists();
        }
        if (is_array($permission)) {
            return $this->permissions()->whereIn('name', $permission)->exists();
        }
        if (is_int($permission)) {
            return $this->permissions()->where('permissions.id', $permission)->exists();
        }
        return false;
    }

    public function hasImplicitPermission(int|array|string $permission): bool
    {
        if (is_string($permission)) {
            return Permission::where('name', $permission)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('roles.id', $this->roles->pluck('id'));
                })->exists();
        }
        if (is_array($permission)) {
            return Permission::whereIn('name', $permission)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('roles.id', $this->roles->pluck('id'));
                })->exists();
        }
        if (is_int($permission)) {
            return Permission::where('permissions.id', $permission)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('roles.id', $this->roles->pluck('id'));
                })->exists();
        }
        return false;
    }

    public function hasPermission($permission): bool
    {
        return $this->hasExplicitPermission($permission) || $this->hasImplicitPermission($permission);
    }

    private function permissionExistsByName(string $permissionName): bool
    {
        return Permission::where('name', $permissionName)->exists();
    }

    private function permissionExistsById(int $permissionId): bool
    {
        return Permission::where('id', $permissionId)->exists();
    }

    public function searchPermissionByName(string $permissionName): ?Permission
    {
        return Permission::where('name', $permissionName)->first() ?? null;
    }

    public function searchPermissionById(int $permissionId): ?Permission
    {
        return Permission::find($permissionId) ?? null;
    }

    private function transformToPermission(int|array|string $permissions): Permission|array|null
    {
        if (is_string($permissions)) {
            return $this->permissionExistsByName($permissions) ? $this->searchPermissionByName($permissions) : null;
        }
        if (is_array($permissions)) {
            $permission = [];
            foreach ($permissions as $permissionName) {
                if ($this->searchPermissionByName($permissionName)) {
                    $permission[] = $this->searchPermissionByName($permissionName);
                }
            }
            return $permission;
        }
        if (is_int($permissions)) {
            return $this->permissionExistsById($permissions) ? $this->searchPermissionById($permissions) : null;
        }
        return null;
    }

    private function attachPermission(Permission $permission): void
    {
        if (!$this->hasExplicitPermission($permission->getKey())) {
            $this->permissions()->syncWithoutDetaching($permission->getKey());
        }
    }

    private function detachPermission(Permission $permission): void
    {
        if ($this->hasExplicitPermission($permission->getKey())) {
            $this->permissions()->detach($permission->getKey());
        }
    }

    public function assignPermission(int|array|string $permissions): void
    {
        $permission = $this->transformToPermission($permissions);
        if (is_array($permission)) {
            foreach ($permission as $perm) {
                $this->assignPermission($perm);
            }
        } else {
            $this->attachPermission($permission);
        }
    }

    public function removePermission(int|array|string $permissions): void
    {
        $permission = $this->transformToPermission($permissions);
        if (is_array($permission)) {
            foreach ($permission as $perm) {
                $this->detachPermission($perm);
            }
        } else {
            $this->detachPermission($permission);
        }
    }

}
