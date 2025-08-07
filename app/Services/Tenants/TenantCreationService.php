<?php

namespace App\Services\Tenants;

use App\Models\Landlord\Role;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\User;
use App\Models\Tenant\Space;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantCreationService
{
    protected ?Tenant $tenant = null;
    protected array $data = [];
    protected array $errors = [];

    public function createTenant(): void
    {
        $this->createTenantRecord();
        $this->createTenantSchema();
        $this->runTenantMigrations();

        $this->configureTenantAdminUsers();
        $this->configureTenantSpace();

        $this->activateTenant();

        if (!empty($this->errors)) {
            $this->rollbackTenant();
            throw new \Exception("Errors during tenant creation");
        }

        Log::info("Tenant created successfully: {$this->tenant->getKey()}");
    }

    public function output(): Tenant
    {
        return $this->tenant;
    }

    public function setData(array $data): TenantCreationService
    {
        $tenantData = [
            'schema'        => $data['schema'],
            'name'          => $data['name'],
            'domain'        => $data['domain'],
            'status'        => $data['status'] ?? Tenant::STATUS_PENDING,
            'created_by'    => $data['created_by'],
            'meta'          => [],
        ];

        $userData = !empty($data['meta']) && !empty($data['meta']['user']) ? $data['meta']['user'] : [];

        $spaceData = !empty($data['meta']) && !empty($data['meta']['spaces']) ? $data['meta']['spaces'] : [];

        $this->data = [
            'tenant'  => $tenantData,
            'user'    => $userData,
            'spaces'   => $spaceData,
        ];

        return $this;
    }

    public function rollbackTenant(): void
    {
        try {
            DB::statement("DROP SCHEMA IF EXISTS {$this->tenant->schema} CASCADE");
            Log::info("Rolled back tenant schema");

            $this->tenant->users()->delete();
            Log::info("Deleted users for tenant");

            $this->tenant->delete();
            Log::info("Deleted tenant");
        } catch (\Exception $e) {
            Log::error("Error during tenant rollback: {$e->getMessage()}");
        }
    }

    public function rollbackSpecificTenant(?Tenant $tenant): void
    {
        try {
            if (!$tenant) return;
            DB::statement("DROP SCHEMA IF EXISTS {$tenant->schema} CASCADE");
            Log::info("Rolled back tenant schema for specific tenant");

            $tenant->users()->delete();
            Log::info("Deleted users for specific tenant");

            $tenant->delete();
            Log::info("Deleted specific tenant");
        } catch (\Exception $e) {
            Log::error("Error during specific tenant rollback: {$e->getMessage()}");
        }
    }

    protected function createTenantRecord(): void
    {
        DB::beginTransaction();

        try {
            $this->tenant = Tenant::create($this->data['tenant']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->errors[] = [
                'action' => 'createTenantRecord',
                'error'  => $e->getMessage(),
            ];
        }
    }

    protected function createTenantSchema(): void
    {
        try {
            DB::statement("CREATE SCHEMA IF NOT EXISTS \"{$this->tenant->schema}\"");
        } catch (\Exception $e) {
            $this->errors[] = [
                'action' => 'createTenantSchema',
                'error'  => $e->getMessage(),
            ];
        }
    }

    protected function runTenantMigrations(): void
    {
        try {
            Artisan::call('tenant:migrate', [
                '--tenantId' => $this->tenant->getKey(),
                '--force'    => true,
                '--seed'     => true,
            ]);
        } catch (\Exception $e) {
            $this->errors[] = [
                'action' => 'runTenantMigrations',
                'error'  => $e->getMessage(),
            ];
        }
    }

    protected function configureTenantAdminUsers(): void
    {
        try {
            $user = User::firstOrCreate(
                ['email' => $this->data['user']['email']],
                [
                    'name'              => $this->data['user']['name'],
                    'password'          => $this->data['user']['password'],
                    'email_verified_at' => now(),
                    'status'            => User::ACTIVE,
                ]
            );
            $user->assignRole(Role::ADMIN);
            $user->assignTenant($this->tenant->getKey());

            $this->data['user'] = $user->toArray();
        } catch (\Exception $e) {
            $this->errors[] = [
                'action' => 'configureTenantAdminUsers',
                'error'  => $e->getMessage(),
            ];
        }
    }

    protected function configureTenantSpace(): void
    {
        try {
            if (empty($this->data['spaces'])) return;

            $space = Space::firstOrCreate(
                ['name' => $this->data['spaces']['name']],
                [
                    'description' => $this->data['spaces']['description'] ?? null,
                    'status'      => Space::STATUS_ACTIVE,
                ]
            );

            $this->data['space'] = $space->toArray();
        } catch (\Exception $e) {
            $this->errors[] = [
                'action' => 'configureTenantSpace',
                'error'  => $e->getMessage(),
            ];
        }
    }

    protected function activateTenant(): void
    {
        try {
            $this->tenant->status = Tenant::STATUS_ACTIVE;
            $this->tenant->save();
        } catch (\Exception $e) {
            $this->errors[] = [
                'action' => 'activateTenant',
                'error'  => $e->getMessage(),
            ];
        }
    }

    protected function reset(): void
    {
        $this->tenant = null;
        $this->data = [];
        $this->errors = [];
    }
}
