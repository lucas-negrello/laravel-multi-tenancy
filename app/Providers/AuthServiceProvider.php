<?php

namespace App\Providers;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\User;
use App\Models\Tenant\Space;
use App\Policies\Landlord\TenantPolicy;
use App\Policies\Landlord\UserPolicy;
use App\Policies\Tenant\SpacePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Tenant::class => TenantPolicy::class,
        Space::class => SpacePolicy::class,
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
