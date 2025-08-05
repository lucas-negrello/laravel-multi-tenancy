<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bootMacros();
        $this->loadLandlordMigrations();
    }

    protected function bootMacros(): void
    {
        Blueprint::macro('registers', function ($created_by = 'created_by', $updated_by = 'updated_by', $nullable = true) {
            $this->unsignedBigInteger($created_by)->nullable($nullable);
            $this->unsignedBigInteger($updated_by)->nullable($nullable);

            $this->foreign($created_by)->references('id')->on('users')->onDelete('cascade');
            $this->foreign($updated_by)->references('id')->on('users')->onDelete('cascade');
        });
    }

    protected function loadLandlordMigrations(): void
    {
        $this->loadMigrationsFrom(database_path('landlord/migrations'));
    }
}
