<?php

namespace App\Policies\Landlord;

use App\Models\Landlord\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isRoot() ||
            $user->hasExplicitPermission('users_view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isRoot() ||
            $user->hasExplicitPermission('users_view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isRoot() ||
            $user->hasExplicitPermission('users_create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->isRoot() ||
            $user->hasExplicitPermission('users_update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->isRoot() ||
            $user->hasExplicitPermission('users_delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->isRoot() ||
            $user->hasExplicitPermission('users_update');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isRoot() ||
            $user->hasExplicitPermission('users_delete');
    }
}
