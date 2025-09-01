<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Add missing columns that exist in the CSV but not in the current table
            if (!Schema::hasColumn('properties', 'link')) {
                $table->string('link')->nullable()->after('id');
            }
            
            if (!Schema::hasColumn('properties', 'status')) {
                $table->string('status')->nullable()->after('longitude');
            }
            
            if (!Schema::hasColumn('properties', 'available_date')) {
                $table->string('available_date')->nullable()->after('property_type');
            }
            
            if (!Schema::hasColumn('properties', 'min_term')) {
                $table->string('min_term')->nullable()->after('available_date');
            }
            
            if (!Schema::hasColumn('properties', 'max_term')) {
                $table->string('max_term')->nullable()->after('min_term');
            }
            
            if (!Schema::hasColumn('properties', 'deposit')) {
                $table->string('deposit')->nullable()->after('max_term');
            }
            
            if (!Schema::hasColumn('properties', 'bills_included')) {
                $table->string('bills_included')->nullable()->after('deposit');
            }
            
            if (!Schema::hasColumn('properties', 'furnishings')) {
                $table->string('furnishings')->nullable()->after('bills_included');
            }
            
            if (!Schema::hasColumn('properties', 'parking')) {
                $table->string('parking')->nullable()->after('furnishings');
            }
            
            if (!Schema::hasColumn('properties', 'garden')) {
                $table->string('garden')->nullable()->after('parking');
            }
            
            if (!Schema::hasColumn('properties', 'broadband')) {
                $table->string('broadband')->nullable()->after('garden');
            }
            
            if (!Schema::hasColumn('properties', 'housemates')) {
                $table->string('housemates')->nullable()->after('broadband');
            }
            
            if (!Schema::hasColumn('properties', 'total_rooms')) {
                $table->string('total_rooms')->nullable()->after('housemates');
            }
            
            if (!Schema::hasColumn('properties', 'smoker')) {
                $table->string('smoker')->nullable()->after('total_rooms');
            }
            
            if (!Schema::hasColumn('properties', 'pets')) {
                $table->string('pets')->nullable()->after('smoker');
            }
            
            if (!Schema::hasColumn('properties', 'occupation')) {
                $table->string('occupation')->nullable()->after('pets');
            }
            
            if (!Schema::hasColumn('properties', 'gender')) {
                $table->string('gender')->nullable()->after('occupation');
            }
            
            if (!Schema::hasColumn('properties', 'couples_ok')) {
                $table->string('couples_ok')->nullable()->after('gender');
            }
            
            if (!Schema::hasColumn('properties', 'smoking_ok')) {
                $table->string('smoking_ok')->nullable()->after('couples_ok');
            }
            
            if (!Schema::hasColumn('properties', 'pets_ok')) {
                $table->string('pets_ok')->nullable()->after('smoking_ok');
            }
            
            if (!Schema::hasColumn('properties', 'pref_occupation')) {
                $table->string('pref_occupation')->nullable()->after('pets_ok');
            }
            
            if (!Schema::hasColumn('properties', 'references')) {
                $table->string('references')->nullable()->after('pref_occupation');
            }
            
            if (!Schema::hasColumn('properties', 'min_age')) {
                $table->string('min_age')->nullable()->after('references');
            }
            
            if (!Schema::hasColumn('properties', 'max_age')) {
                $table->string('max_age')->nullable()->after('min_age');
            }
            
            if (!Schema::hasColumn('properties', 'photo_count')) {
                $table->string('photo_count')->nullable()->after('max_age');
            }
            
            if (!Schema::hasColumn('properties', 'first_photo_url')) {
                $table->string('first_photo_url')->nullable()->after('photo_count');
            }
            
            if (!Schema::hasColumn('properties', 'all_photos')) {
                $table->text('all_photos')->nullable()->after('first_photo_url');
            }
            
            if (!Schema::hasColumn('properties', 'photos')) {
                $table->json('photos')->nullable()->after('all_photos');
            }
            
            // Add any additional columns that might be needed
            if (!Schema::hasColumn('properties', 'contact_info')) {
                $table->text('contact_info')->nullable()->after('photos');
            }
            
            if (!Schema::hasColumn('properties', 'management_company')) {
                $table->string('management_company')->nullable()->after('contact_info');
            }
            
            if (!Schema::hasColumn('properties', 'amenities')) {
                $table->text('amenities')->nullable()->after('management_company');
            }
            
            if (!Schema::hasColumn('properties', 'balcony_roof_terrace')) {
                $table->string('balcony_roof_terrace')->nullable()->after('amenities');
            }
            
            if (!Schema::hasColumn('properties', 'disabled_access')) {
                $table->string('disabled_access')->nullable()->after('balcony_roof_terrace');
            }
            
            if (!Schema::hasColumn('properties', 'living_room')) {
                $table->string('living_room')->nullable()->after('disabled_access');
            }
        });
        
        // Modify existing columns to accommodate larger data and ensure proper types
        // Note: Skipping column type changes to avoid data truncation issues
        // The existing column types should be sufficient for the CSV data
        
        /*
        Schema::table('properties', function (Blueprint $table) {
            // Make description field as large as possible (LONGTEXT can store up to 4GB)
            if (Schema::hasColumn('properties', 'description')) {
                try {
                    $table->longText('description')->change();
                } catch (Exception $e) {
                    // If change fails, log it but continue
                    \Log::warning('Could not change description column type: ' . $e->getMessage());
                }
            }
            
            // Make other text fields larger if needed - only if they don't have data that would be truncated
            if (Schema::hasColumn('properties', 'location')) {
                try {
                    $table->text('location')->change();
                } catch (Exception $e) {
                    // If change fails, log it but continue
                    \Log::warning('Could not change location column type: ' . $e->getMessage());
                }
            }
            
            if (Schema::hasColumn('properties', 'title')) {
                try {
                    $table->text('title')->change();
                } catch (Exception $e) {
                    // If change fails, log it but continue
                    \Log::warning('Could not change title column type: ' . $e->getMessage());
                }
            }
            
            // Ensure price field can handle the format from CSV (e.g., "£885.00")
            if (Schema::hasColumn('properties', 'price')) {
                try {
                    $table->string('price')->change();
                } catch (Exception $e) {
                    // If change fails, log it but continue
                    \Log::warning('Could not change price column type: ' . $e->getMessage());
                }
            }
            
            // Ensure latitude and longitude are properly typed
            if (Schema::hasColumn('properties', 'latitude')) {
                try {
                    $table->decimal('latitude', 10, 8)->change();
                } catch (Exception $e) {
                    // If change fails, log it but continue
                    \Log::warning('Could not change latitude column type: ' . $e->getMessage());
                }
            }
            
            if (Schema::hasColumn('properties', 'longitude')) {
                try {
                    $table->decimal('longitude', 11, 8)->change();
                } catch (Exception $e) {
                    // If change fails, log it but continue
                    \Log::warning('Could not change longitude column type: ' . $e->getMessage());
                }
            }
        });
        */
        
        // Clean up any duplicate or inconsistent columns that might exist
        $this->cleanupDuplicateColumns();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Drop added columns
            $columnsToDrop = [
                'link', 'status', 'available_date', 'min_term', 'max_term', 'deposit',
                'bills_included', 'furnishings', 'parking', 'garden', 'broadband',
                'housemates', 'total_rooms', 'smoker', 'pets', 'occupation', 'gender',
                'couples_ok', 'smoking_ok', 'pets_ok', 'pref_occupation', 'references',
                'min_age', 'max_age', 'photo_count', 'first_photo_url', 'all_photos',
                'photos', 'contact_info', 'management_company', 'amenities', 
                'balcony_roof_terrace', 'disabled_access', 'living_room'
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('properties', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            // Note: Column type changes were skipped to avoid data truncation issues
            // No need to revert them
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

