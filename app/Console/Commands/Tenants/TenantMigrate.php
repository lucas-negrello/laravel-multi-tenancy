<?php

namespace App\Console\Commands\Tenants;

use Illuminate\Console\Command;

class TenantMigrate extends TenantMigration
{
    protected $signature = 'tenant:migrate
                            {--all : Run migrations for all tenants}
                            {--tenantId=* : Run migrations for specified tenant IDs}
                            {--database=tenant : The database connection to use}
                            {--path=database/tenant/migrations : The path for tenant migrations}
                            {--env= : The environment}
                            {--force : Force the operation to run in production}
                            {--seed : Indicates if the seed task should be re-run}
                            {--seeder= : The class name of the root seeder}
                            {--step : Force the migrations to be run so they can be rolled back individually}';

    protected $description = 'Migrate command for tenants';

    protected string $migrationCommand = 'migrate';
}
