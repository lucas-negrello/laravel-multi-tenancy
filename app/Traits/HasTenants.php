<?php

namespace App\Traits;

use App\Models\Landlord\User;
use App\Services\Utils\Landlord\UserService;
use Illuminate\Database\Eloquent\Builder;

trait HasTenants
{
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
