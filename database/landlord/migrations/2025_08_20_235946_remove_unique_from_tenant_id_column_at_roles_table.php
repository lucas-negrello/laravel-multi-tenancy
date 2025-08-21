<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Remove unique constraint from tenant_id column
            $table->dropUnique(['tenant_id']);

            // Add index to tenant_id column
            $table->index('tenant_id');

            // Add index to is_tenant_base column
            $table->index('is_tenant_base');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Remove index from tenant_id column
            $table->dropIndex(['tenant_id']);

            // Remove index from is_tenant_base column
            $table->dropIndex(['is_tenant_base']);

            // Re-add unique constraint to tenant_id column
            $table->unique('tenant_id');
        });
    }
};
