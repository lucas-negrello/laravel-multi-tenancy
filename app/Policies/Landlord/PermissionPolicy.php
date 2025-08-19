<?php

namespace App\Policies\Landlord;

use App\Models\Landlord\Permission;
use App\Models\Landlord\User;
use App\Policies\Base\BasePolicy;
use Illuminate\Auth\Access\Response;

class PermissionPolicy extends BasePolicy
{

    protected function getModelName(): ?string
    {
        return 'permissions';
    }
}
