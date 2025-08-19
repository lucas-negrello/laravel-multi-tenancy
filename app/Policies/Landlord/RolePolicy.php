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
}
