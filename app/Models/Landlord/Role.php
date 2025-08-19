<?php

namespace App\Models\Landlord;

use App\Models\Base\LandlordModel;
use App\Traits\HasTenants;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends LandlordModel
{
    use HasTenants;

    const
        ROOT = 'root',
        ROOT_ADMIN = 'root_admin',
        ROOT_MANAGER = 'root_manager',
        ROOT_USER = 'root_user';

    const
        ADMIN = 'admin',
        MANAGER = 'manager',
        USER = 'user',
        GUEST = 'guest';

    const ROOT_ROLES = [
        self::ROOT,
        self::ROOT_ADMIN,
        self::ROOT_MANAGER,
        self::ROOT_USER,
    ];

    const TENANT_ROLES = [
        self::ADMIN,
        self::MANAGER,
        self::USER,
        self::GUEST,
    ];

    const ROLES = [
        ...self::ROOT_ROLES,
        ...self::TENANT_ROLES,
    ];

    protected $fillable = [
        'name',
        'description',
        'is_tenant_base',
        'created_by',
        'updated_by',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role_id',
            'permission_id');
    }

    public function tenants()
    {
        return Tenant::whereHas('users.roles', function ($q) {
            $q->where('roles.id', $this->getKey());
        })->get();
    }
}
