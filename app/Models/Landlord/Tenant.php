<?php

namespace App\Models\Landlord;

use App\Traits\HasDefaultStatus;
use App\Traits\HasRegisters;
use App\Traits\TenantSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

class Tenant extends Model
{
    use TenantSchema, SoftDeletes, HasRegisters, HasDefaultStatus;

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
