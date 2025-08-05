<?php

namespace App\Models\Base;


use App\Scopes\TenantScope;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class TenantModel extends BaseModel
{
    use UsesTenantConnection;

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }
}
