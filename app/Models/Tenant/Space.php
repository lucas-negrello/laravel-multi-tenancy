<?php

namespace App\Models\Tenant;

use App\Models\Base\TenantModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Space extends TenantModel
{
    use SoftDeletes;

    const
        STATUS_PENDING = 0,
        STATUS_ACTIVE = 1,
        STATUS_INACTIVE = 2,
        STATUS_SUSPENDED = 3,
        STATUS_DELETED = -1;

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
        self::STATUS_SUSPENDED,
        self::STATUS_DELETED,
    ];

    protected $fillable = [
        'name',
        'description',
        'status',
        'meta'
    ];

    protected $casts = [
        'meta' => 'array'
    ];
}
