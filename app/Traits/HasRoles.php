<?php

namespace App\Traits;

use App\Models\Landlord\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this
            ->belongsToMany(Role::class, 'users_roles_permissions', 'user_id', 'role_id')
            ->withTimestamps();
    }

    public function isRoot(): bool
    {
        return $this->hasRole($this->rootRoles());
    }

    public function rootRoles($key = 'name')
    {
        return Role::where('is_tenant_base', false)->get([$key])->pluck($key)->toArray();
    }

    public function hasRole(int|array|string $roles): bool
    {
        if (is_array($roles)) {
            return $this->roles()->whereIn('roles.name', $roles)->exists();
        }
        if (is_string($roles)) {
            return $this->roles()->where('roles.name', $roles)->exists();
        }
        if (is_int($roles)) {
            return $this->roles()->where('roles.id', $roles)->exists();
        }
        return false;
    }

    private function roleExistsByName(string $roleName): bool
    {
        return Role::where('name', $roleName)->exists();
    }

    public function roleExistsById(string $roleId): bool
    {
        return Role::where('id', $roleId)->exists();
    }

    public function searchRoleByName(string $roleName): ?Role
    {
        if (!$this->roleExistsByName($roleName)) {
            return null;
        }
        return Role::where('name', $roleName)->first();
    }

    public function searchRoleById(int $roleId): ?Role
    {
        if (!$this->roleExistsById($roleId)) {
            return null;
        }
        return Role::find($roleId);
    }

    public function transformToRole(int|array|string $roles): array|Role|null
    {
        if (is_array($roles)) {
            $role = [];
            foreach ($roles as $rl) {
                if (!!$this->searchRoleByName($rl)) {
                    $role[] = $this->searchRoleByName($rl);
                }
            }
            return $role;
        }
        if (is_string($roles)) {
            return $this->searchRoleByName($roles);
        }
        if (is_int($roles)) {
            return $this->searchRoleById($roles);
        }
        return null;
    }

    public function attachRole(Role $role): void
    {
        if (!$this->hasRole($role->getKey())) {
            $this->roles()->syncWithoutDetaching($role->getKey());
        }
    }

    public function detachRole(Role $role): void
    {
        if ($this->hasRole($role->getKey())) {
            $this->roles()->detach($role->getKey());
        }
    }

    public function assignRole(int|array|string $roles): void
    {
        $role = $this->transformToRole($roles);
        if (is_array($role)) {
            foreach ($role as $rl) {
               $this->attachRole($rl);
            }
        } else {
            $this->attachRole($role);
        }
    }

    public function removeRole(int|array|string $roles): void
    {
        $role = $this->transformToRole($roles);
        if (is_array($role)) {
            foreach ($role as $rl) {
                $this->detachRole($rl);
            }
        } else {
            $this->detachRole($role);
        }
    }

}
