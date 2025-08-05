<?php

namespace App\Models\Landlord;

use App\Traits\HasRegisters;
use App\Traits\TenantSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

class Tenant extends Model
{
    use TenantSchema, SoftDeletes, HasRegisters;

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
        'schema',
        'name',
        'domain',
        'status',
        'meta',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'tenant_user',
            'tenant_id',
            'user_id');
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->whereIn(
            'status', [
                Tenant::STATUS_ACTIVE,
                Tenant::STATUS_SUSPENDED
            ]);
    }
}
