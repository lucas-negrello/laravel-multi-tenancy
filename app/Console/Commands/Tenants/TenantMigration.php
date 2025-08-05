<?php

namespace App\Console\Commands\Tenants;

use App\Models\Landlord\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

abstract class TenantMigration extends Command
{
    protected string $migrationCommand;

    protected int $chunk = 100;

    public function handle(): int
    {
        if ($this->option('all')) {
            $tenants = Tenant::all(['id', 'schema']);
        } elseif ($this->option('tenantId')) {
            $tenantIds = !is_array($this->option('tenantId')) ? explode(',', $this->option('tenantId')) : $this->option('tenantId');
            $tenants = Tenant::whereIn('id', $tenantIds)->get(['id', 'schema']);
        } else {
            $this->error("You must provide either the --all option or at least one --tenantId.");
            return 1;
        }

        $options = [
            '--database' =>     $this->option('database'),
            '--path' =>         $this->option('path'),
            '--env' =>          $this->option('env'),
            '--force' =>        $this->option('force'),
        ];

        if ($this->option('seed')) {
            $options['--seed'] = true;
            $options['--seeder'] = $this->option('seeder') ?: 'Database\\Tenant\\Seeders\\DatabaseSeeder';
        }

        $options = array_filter($options, fn($value) => !is_null($value) && $value !== '');

        $tenants
            ->chunk($this->chunk)
            ->each(function ($chunk) use ($options) {
                $chunk
                    ->each(function ($tenant) use ($options) {
                        $this->info("Running {$this->migrationCommand} for tenant: {$tenant->schema} (ID: {$tenant->getKey()})");

                        $tenant->makeCurrent(true);

                        Artisan::call($this->migrationCommand, $options);

                        $this->info(Artisan::output());

                        $tenant->forget();
                    });
            });

        return 0;
    }
}
