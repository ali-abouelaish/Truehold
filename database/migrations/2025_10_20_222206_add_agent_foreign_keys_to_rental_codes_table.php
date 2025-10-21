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
            // Add new foreign key columns
            $table->unsignedBigInteger('rental_agent_id')->nullable()->after('rent_by_agent');
            $table->unsignedBigInteger('marketing_agent_id')->nullable()->after('marketing_agent');
            
            // Add foreign key constraints
            $table->foreign('rental_agent_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('marketing_agent_id')->references('id')->on('users')->onDelete('set null');
            
            // Add indexes for better performance
            $table->index('rental_agent_id');
            $table->index('marketing_agent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_codes', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['rental_agent_id']);
            $table->dropForeign(['marketing_agent_id']);
            
            // Drop indexes
            $table->dropIndex(['rental_agent_id']);
            $table->dropIndex(['marketing_agent_id']);
            
            // Drop columns
            $table->dropColumn(['rental_agent_id', 'marketing_agent_id']);
        });
    }
};