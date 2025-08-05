<?php

namespace App\Services\Tenants;

use App\Models\Landlord\Role;
use App\Models\Landlord\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantResolverService
{
    public static function resolve(Request $request): ?Tenant
    {
        $tenantIdentifier = $request->header('X-Tenant-Identifier');
        $host = $request->getHost();
        $origin = $request->header('Origin');

        if (!empty($tenantIdentifier)) {
            $tenant = Tenant::find($tenantIdentifier);
        } else {
            $tenant = Tenant::whereIn('domain', [$origin, $host])->first();
        }

        return $tenant;
    }

    public static function userHasAccess(Tenant $tenant): bool
    {
        $user = Auth::user();

        if ($user && $user->hasRole([...Role::ROOT_ROLES])) return true;

        return $user && $user->tenants()->where('tenants.id', $tenant->getKey())->exists();
    }
}
