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
        Schema::table('rental_codes', function (Blueprint $table) {
            // Remove old agent name columns since we now use foreign keys
            $table->dropColumn(['agent_name', 'marketing_agent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_codes', function (Blueprint $table) {
            // Add back the old columns if we need to rollback
            $table->string('agent_name')->nullable();
            $table->string('marketing_agent')->nullable();
        });
    }
};