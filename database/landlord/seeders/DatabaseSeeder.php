<?php

namespace Database\Seeders;

use App\Models\Landlord\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            RootSetupSeeder::class,
            TenantSetupSeeder::class
        ]);
    }
}
