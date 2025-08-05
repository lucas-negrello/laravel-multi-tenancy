<?php

namespace App\Console\Commands\Tenants;

use App\Console\Commands\Tenants\TenantMigration;

class TenantSeed extends TenantMigration
{
    protected $signature = 'tenant:seed
                            {--all : Run seeder for all tenants}
                            {--tenantId=* : Run migrations for specified tenant IDs}
                            {--database=tenant : The database connection to use}
                            {--path=database/tenant/migrations : The path for tenant migrations}
                            {--env= : The environment}
                            {--force : Force the operation to run in production}';

    protected $description = 'Drop all tenant tables and re-run tenant migrations';

    protected string $migrationCommand = 'migrate:seed';
}

