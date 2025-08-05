<?php

namespace App\Policies\Tenant;

use App\Models\Landlord\User;
use App\Models\Tenant\Space;
use Illuminate\Auth\Access\Response;

class SpacePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isRoot() ||
            $user->hasPermission('spaces_view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Space $space): bool
    {
        return $user->isRoot() ||
            $user->hasPermission('spaces_view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isRoot() ||
            $user->hasPermission('spaces_create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Space $space): bool
    {
        return $user->isRoot() ||
            $user->hasPermission('spaces_update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Space $space): bool
    {
        return $user->isRoot() ||
            $user->hasPermission('spaces_delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Space $space): bool
    {
        return $user->isRoot() ||
            $user->hasPermission('spaces_update');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Space $space): bool
    {
        return $user->isRoot() ||
            $user->hasPermission('spaces_delete');
    }
}
