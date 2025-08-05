<?php

namespace App\Console\Commands\Tenants;

use App\Console\Commands\Tenants\TenantMigration;

class TenantMigrateFresh extends TenantMigration
{
    protected $signature = 'tenant:migrate:fresh
                            {--all : Run migrations for all tenants}
                            {--tenantId=* : Run migrations for specified tenant IDs}
                            {--database=tenant : The database connection to use}
                            {--path=database/tenant/migrations : The path for tenant migrations}
                            {--env= : The environment}
                            {--force : Force the operation to run in production}';

    protected $description = 'Drop all tenant tables and re-run tenant migrations';

    protected string $migrationCommand = 'migrate:fresh';
}
