<?php

namespace App\Models\Landlord;

use App\Models\Base\BaseModel;

class TenantUser extends BaseModel
{
    protected $table = 'tenants_users';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'created_by',
        'updated_by',
    ];
}
