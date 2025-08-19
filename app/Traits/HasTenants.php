<?php

namespace App\Traits;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\User;
use App\Services\Utils\Landlord\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasTenants
{
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(
            Tenant::class,
            'tenants_users',
            'user_id',
            'tenant_id'
        )->withTimestamps();
    }

    public function hasTenant(string $tenantId): bool
    {
        return $this->tenants()->where('tenants.id', $tenantId)->exists();
    }

    public function assignTenant(string $tenantId): void
    {
        if (!$this->hasTenant($tenantId)) {
            $this->tenants()->syncWithoutDetaching([$tenantId]);
        }
    }

    public function detachTenant(string $tenantId): void
    {
        if ($this->hasTenant($tenantId)) {
            $this->tenants()->detach($tenantId);
        }
    }

    public function scopeVerifiedTenantUser(Builder $query): Builder | User
    {
        $currentTenant = tenant();
        $currentUser = UserService::getLoggedUser();

        if ($currentTenant && !$currentUser->isRoot()) {
            $query->whereHas('tenants', function($q) use ($currentTenant) {
                $q->where('tenants.id', $currentTenant->getKey());
            });
        }

        return $query;
    }
}
