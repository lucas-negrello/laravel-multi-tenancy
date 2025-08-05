<?php

namespace Database\Seeders;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\User;
use App\Services\Tenants\TenantCreationService;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSetupSeeder extends Seeder
{
    private array $data = [];

    /**
     * Run the database seeds.
     */
    public function run(TenantCreationService $service): void
    {
        if (!env('RUN_TENANT_SETUP_SEEDER', false)) return;

        $this->dataGenerator();
        $this->reset();
        $this->setup($service);

    }

    private function setup(TenantCreationService $service): void
    {
        try {
            foreach ($this->data as $item) {
                $service
                    ->setData($item)
                    ->createTenant();
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    private function dataGenerator(): void
    {
        $user = [
            'name'                      => 'Space Tester User',
            'email'                     => 'user@tester.com',
            'password'                  => 'password',
            'password_confirmation'     => 'password',
        ];

        $multitenantUser = [
            'name'                      => 'Space Tester Multitenant User',
            'email'                     => 'multi@tester.com',
            'password'                  => 'password',
            'password_confirmation'     => 'password',
        ];

        $this->data[] = [
            'schema'                    => 'simple_tenant_schema',
            'name'                      => 'Simple Tenant',
            'domain'                    => 'localhost',
            'status'                    => Tenant::STATUS_PENDING,
            'created_by'                => User::orderBy('id')->first()->getKey(),
            'meta'                      => [
                'user'                  => $user,
                'space'                 => [
                    'name'              => 'Simple Space',
                    'description'       => 'This is a simple space for testing purposes.',
                ]
            ]
        ];

        for ($i = 1; $i < 3; $i++) {
            $this->data[] = [
                'schema'                => 'multitenant_tenant_schema_' . $i,
                'name'                  => 'Multitenant Tenant ' . $i,
                'domain'                => 'localhost',
                'status'                => Tenant::STATUS_PENDING,
                'created_by'            => User::orderBy('id')->first()->getKey(),
                'meta'                  => [
                    'user'              => $multitenantUser,
                    'space'             => [
                        'name'          => 'Multitenant Space ' . $i,
                        'description'   => 'This is a multitenant space for testing purposes.',
                    ]
                ]
            ];
        }
    }

    private function reset()
    {
        foreach ($this->data as $data) {
            DB::statement("DROP SCHEMA IF EXISTS {$data['schema']} CASCADE");
        }
    }


}
