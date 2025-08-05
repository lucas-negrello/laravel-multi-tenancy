<?php

namespace App\Http\Middleware;

use App\Services\Tenants\TenantResolverService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = TenantResolverService::resolve($request);

        if (!$tenant)
            return apiError('Tenant not found.', 404);

        $tenant->makeCurrent();

        if (!TenantResolverService::userHasAccess($tenant))
            return apiError('Access denied for this tenant.', 403);

        return $next($request);
    }
}
