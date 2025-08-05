<?php

namespace App\Scopes;

use App\Services\Tenants\TenantResolverService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenant = TenantResolverService::resolve(request());

        $tenant?->makeCurrent();
    }
}
