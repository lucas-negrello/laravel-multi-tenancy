<?php

namespace App\Traits;

use App\Models\Landlord\Tenant;
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
        );
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
}
