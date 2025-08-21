<?php

namespace App\Models\Landlord;

use App\Models\Base\LandlordModel;
use App\Traits\HasTenants;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends LandlordModel
{
    use HasTenants;

    const BASE_PERMISSIONS = [
        self::CREATE,
        self::VIEW,
        self::UPDATE,
        self::DELETE,
    ];

    const
        CREATE = 'create',
        VIEW = 'view',
        UPDATE = 'update',
        DELETE = 'delete';

    const RESOURCES = [
        ...self::ROOT_RESOURCES,
        ...self::TENANT_RESOURCES,
    ];

    const ROOT_RESOURCES = [
        self::USERS,
        self::ROLES,
        self::PERMISSIONS,
        self::TENANTS,
    ];

    const TENANT_RESOURCES = [
        self::SPACES,
    ];

    const
        USERS = 'users',
        ROLES = 'roles',
        PERMISSIONS = 'permissions',
        TENANTS = 'tenants',
        SPACES = 'spaces';

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'updated_by',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_permissions',
            'permission_id',
            'role_id');
    }

    public function tenants()
    {
        return Tenant::whereHas('users.roles', function ($q) {
            $q->whereHas('roles.permissions', function ($q) {
                $q->where('permissions.id', $this->getKey());
            });
        })->get();
    }
}
