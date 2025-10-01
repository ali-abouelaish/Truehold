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
        Schema::table('clients', function (Blueprint $table) {
            // Drop the old single field
            if (Schema::hasColumn('clients', 'current_landlord_contact')) {
                $table->dropColumn('current_landlord_contact');
            }
            
            // Add the new separate fields
            if (!Schema::hasColumn('clients', 'current_landlord_name')) {
                $table->string('current_landlord_name')->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('clients', 'current_landlord_contact_info')) {
                $table->text('current_landlord_contact_info')->nullable()->after('current_landlord_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Drop the new fields
            if (Schema::hasColumn('clients', 'current_landlord_name')) {
                $table->dropColumn('current_landlord_name');
            }
            if (Schema::hasColumn('clients', 'current_landlord_contact_info')) {
                $table->dropColumn('current_landlord_contact_info');
            }
            
            // Restore the old single field
            if (!Schema::hasColumn('clients', 'current_landlord_contact')) {
                $table->text('current_landlord_contact')->nullable()->after('notes');
            }
        });
    }
};
