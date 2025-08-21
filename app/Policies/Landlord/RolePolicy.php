<?php

namespace App\Policies\Landlord;

use App\Models\Landlord\Role;
use App\Models\Landlord\User;
use App\Policies\Base\BasePolicy;
use Illuminate\Auth\Access\Response;

class RolePolicy extends BasePolicy
{

    protected function getModelName(): ?string
    {
        return 'roles';
    }

    /**
     * @param User $user
     * @param Role $model
     * @return bool
     */
    public function view(User $user, $model): bool
    {
        if (tenant() && !$model->is_tenant_base) return false;
        return $this->can($user, static::VIEW_PERMISSION, $model);
    }

    /**
     * @param User $user
     * @param Role $model
     * @return bool
     */
    public function delete(User $user, $model): bool
    {
        if (!(!tenant() || (!tenant() && $model->is_tenant_base))) return false;
        return $this->can($user, static::DELETE_PERMISSION, $model);
    }

    /**
     * @param User $user
     * @param Role $model
     * @return bool
     */
    public function forceDelete(User $user, $model): bool
    {
        if (!(!tenant() || (!tenant() && $model->is_tenant_base))) return false;
        return $this->can($user, static::DELETE_PERMISSION, $model);
    }
}
