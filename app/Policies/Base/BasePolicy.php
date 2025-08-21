<?php

namespace App\Policies\Base;

use App\Models\Landlord\User;

abstract class BasePolicy
{
    private array|null $permissions = null;

    const
        VIEW_PERMISSION = 'view',
        CREATE_PERMISSION = 'create',
        UPDATE_PERMISSION = 'update',
        DELETE_PERMISSION = 'delete';

    public function __construct()
    {
        $this->definePermission();
    }

    protected function definePermission(): void
    {
        $model = $this->getModelName();
        if ($model)
        {
            $this->permissions = [
                static::VIEW_PERMISSION   => "{$model}_view",
                static::CREATE_PERMISSION => "{$model}_create",
                static::UPDATE_PERMISSION => "{$model}_update",
                static::DELETE_PERMISSION => "{$model}_delete",
            ];
        }
    }

    abstract protected function getModelName(): ?string;

    /**
     * @return bool
     * Override this method to add extra general validation logic
     * that applies to all actions in the policy.
     */
    protected function extraGeneralValidation(): bool
    {
        return true;
    }

    protected function can(User $user, string $action, $model = null): bool
    {
        if (!$this->extraGeneralValidation()) return false;
        if ($user->isRoot()) return true;
        $permission = $this->permissions[$action] ?? null;
        if (!$permission) return false;

        if (function_exists('tenant') && $tenant = tenant()) {
            $belongsToTenant = $user->hasTenant($tenant->getKey());
            if ($model && method_exists($model, 'tenants') && method_exists($model, 'hasTenant')) {
                $modelBelongsToTenant = $model->hasTenant($tenant->getKey());
                return $belongsToTenant && $modelBelongsToTenant && $user->hasPermission($permission);
            }
            return $belongsToTenant && $user->hasPermission($permission);
        }

        return $user->hasPermission($permission);
    }

    public function viewAny(User $user): bool
    {
        return $this->can($user, static::VIEW_PERMISSION);
    }

    public function view(User $user, $model): bool
    {
        return $this->can($user, static::VIEW_PERMISSION, $model);
    }

    public function create(User $user): bool
    {
        return $this->can($user, static::CREATE_PERMISSION);
    }

    public function update(User $user, $model): bool
    {
        return $this->can($user, static::UPDATE_PERMISSION, $model);
    }
    public function delete(User $user, $model): bool
    {
        return $this->can($user, static::DELETE_PERMISSION, $model);
    }
    public function restore(User $user, $model): bool
    {
        return $this->can($user, static::UPDATE_PERMISSION, $model);
    }
    public function forceDelete(User $user, $model): bool
    {
        return $this->can($user, static::DELETE_PERMISSION, $model);
    }

}
