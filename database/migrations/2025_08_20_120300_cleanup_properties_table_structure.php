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
        Schema::table('properties', function (Blueprint $table) {
            // Add missing columns that don't exist yet
            if (!Schema::hasColumn('properties', 'couples_ok')) {
                $table->string('couples_ok')->nullable();
            }
            
            if (!Schema::hasColumn('properties', 'smoking_ok')) {
                $table->string('smoking_ok')->nullable();
            }
            
            if (!Schema::hasColumn('properties', 'pets_ok')) {
                $table->string('pets_ok')->nullable();
            }
            
            if (!Schema::hasColumn('properties', 'pref_occupation')) {
                $table->string('pref_occupation')->nullable();
            }
        });

        // Clean up duplicate and inconsistent columns
        $this->cleanupDuplicateColumns();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn(['couples_ok', 'smoking_ok', 'pets_ok', 'pref_occupation']);
        });
    }

    /**
     * Clean up duplicate and inconsistent columns.
     */
    private function cleanupDuplicateColumns()
    {
        // Get the database connection
        $connection = DB::connection();
        
        // List of columns to drop (duplicates or unused)
        $columnsToDrop = [
            'url', // We have 'link' now
            'age', // We have 'min_age' now
            'ages', // We have 'max_age' now
            'any_pets', // We have 'pets' now
            'available', // We have 'status' now
            'broadband_included', // We have 'broadband' now
            'couples_allowed', // We have 'couples_ok' now
            'garage', // We have 'parking' now
            'garden_patio', // We have 'garden' now
            'maximum_term', // We have 'max_term' now
            'minimum_term', // We have 'min_term' now
            'number', // We have 'total_rooms' now
            'number_housemates', // We have 'housemates' now
            'number_flatmates', // Redundant
            'pets_allowed', // We have 'pets_ok' now
            'room_type', // Redundant with property_type
            'smalloweding_allowed', // Typo, not used
            'total_number_rooms', // We have 'total_rooms' now
            'vegetarian_vegan', // Not relevant for properties
        ];

        foreach ($columnsToDrop as $column) {
            if (Schema::hasColumn('properties', $column)) {
                $connection->statement("ALTER TABLE properties DROP COLUMN {$column}");
            }
        }

        // Rename some columns for consistency
        if (Schema::hasColumn('properties', 'number_housemates')) {
            $connection->statement("ALTER TABLE properties CHANGE number_housemates housemates VARCHAR(255) NULL");
        }
    }
};
