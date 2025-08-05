<?php

namespace App\Models\Landlord;

use App\Models\Base\LandlordModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends LandlordModel
{

    const BASE_PERMISSIONS = [
        self::CREATE,
        self::READ,
        self::UPDATE,
        self::DELETE,
    ];

    const
        CREATE = 'create',
        READ = 'read',
        UPDATE = 'update',
        DELETE = 'delete';

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
}
