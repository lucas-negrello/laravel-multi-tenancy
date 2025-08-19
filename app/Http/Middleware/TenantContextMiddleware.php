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
            return errorResponse('Tenant not found.', 404);

        app()->instance('tenant', $tenant);

        $tenant->makeCurrent(true);

        if (!TenantResolverService::userHasAccess($request, $tenant))
            return errorResponse('Access denied for this tenant.', 403);

        return $next($request);
    }
}
