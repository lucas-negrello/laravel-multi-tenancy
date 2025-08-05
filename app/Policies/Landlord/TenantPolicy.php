<?php

namespace App\Policies\Landlord;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\User;

class TenantPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isRoot() ||
               $user->hasExplicitPermission('tenants_view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tenant $tenant): bool
    {
        return $user->isRoot() ||
            $user->hasExplicitPermission('tenants_view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isRoot() ||
            $user->hasExplicitPermission('tenants_create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tenant $tenant): bool
    {
        return $user->isRoot() ||
            $user->hasExplicitPermission('tenants_update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tenant $tenant): bool
    {
        return $user->isRoot() ||
            $user->hasExplicitPermission('tenants_delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tenant $tenant): bool
    {
        return $user->isRoot() ||
            $user->hasExplicitPermission('tenants_update');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tenant $tenant): bool
    {
        return $user->isRoot() ||
            $user->hasExplicitPermission('tenants_delete');
    }
}
