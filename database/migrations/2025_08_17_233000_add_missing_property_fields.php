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
            // Add only the fields that don't already exist
            // These are the additional fields from the advanced scraper
            if (!Schema::hasColumn('properties', 'age')) {
                $table->string('age')->nullable()->after('amenities');
            }
            if (!Schema::hasColumn('properties', 'ages')) {
                $table->string('ages')->nullable()->after('age');
            }
            if (!Schema::hasColumn('properties', 'any_pets')) {
                $table->string('any_pets')->nullable()->after('ages');
            }
            if (!Schema::hasColumn('properties', 'balcony_roof_terrace')) {
                $table->string('balcony_roof_terrace')->nullable()->after('any_pets');
            }
            if (!Schema::hasColumn('properties', 'bills_included')) {
                $table->string('bills_included')->nullable()->after('balcony_roof_terrace');
            }
            if (!Schema::hasColumn('properties', 'broadband_included')) {
                $table->string('broadband_included')->nullable()->after('bills_included');
            }
            if (!Schema::hasColumn('properties', 'couples_allowed')) {
                $table->string('couples_allowed')->nullable()->after('broadband_included');
            }
            if (!Schema::hasColumn('properties', 'deposit_room_1')) {
                $table->string('deposit_room_1')->nullable()->after('couples_allowed');
            }
            if (!Schema::hasColumn('properties', 'deposit_room_2')) {
                $table->string('deposit_room_2')->nullable()->after('deposit_room_1');
            }
            if (!Schema::hasColumn('properties', 'deposit_room_3')) {
                $table->string('deposit_room_3')->nullable()->after('deposit_room_2');
            }
            if (!Schema::hasColumn('properties', 'deposit_room_4')) {
                $table->string('deposit_room_4')->nullable()->after('deposit_room_3');
            }
            if (!Schema::hasColumn('properties', 'disabled_access')) {
                $table->string('disabled_access')->nullable()->after('deposit_room_4');
            }
            if (!Schema::hasColumn('properties', 'garage')) {
                $table->string('garage')->nullable()->after('disabled_access');
            }
            if (!Schema::hasColumn('properties', 'garden_patio')) {
                $table->string('garden_patio')->nullable()->after('garage');
            }
            if (!Schema::hasColumn('properties', 'gender')) {
                $table->string('gender')->nullable()->after('garden_patio');
            }
            if (!Schema::hasColumn('properties', 'living_room')) {
                $table->string('living_room')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('properties', 'max_age')) {
                $table->string('max_age')->nullable()->after('living_room');
            }
            if (!Schema::hasColumn('properties', 'min_age')) {
                $table->string('min_age')->nullable()->after('max_age');
            }
            if (!Schema::hasColumn('properties', 'number_housemates')) {
                $table->string('number_housemates')->nullable()->after('min_age');
            }
            if (!Schema::hasColumn('properties', 'number_flatmates')) {
                $table->string('number_flatmates')->nullable()->after('number_housemates');
            }
            if (!Schema::hasColumn('properties', 'occupation')) {
                $table->string('occupation')->nullable()->after('number_flatmates');
            }
            if (!Schema::hasColumn('properties', 'pets_allowed')) {
                $table->string('pets_allowed')->nullable()->after('occupation');
            }
            if (!Schema::hasColumn('properties', 'references')) {
                $table->string('references')->nullable()->after('pets_allowed');
            }
            if (!Schema::hasColumn('properties', 'room_type')) {
                $table->string('room_type')->nullable()->after('references');
            }
            if (!Schema::hasColumn('properties', 'smalloweding_allowed')) {
                $table->string('smalloweding_allowed')->nullable()->after('room_type');
            }
            if (!Schema::hasColumn('properties', 'total_number_rooms')) {
                $table->string('total_number_rooms')->nullable()->after('smalloweding_allowed');
            }
            if (!Schema::hasColumn('properties', 'vegetarian_vegan')) {
                $table->string('vegetarian_vegan')->nullable()->after('total_number_rooms');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'age',
                'ages',
                'any_pets',
                'balcony_roof_terrace',
                'bills_included',
                'broadband_included',
                'couples_allowed',
                'deposit_room_1',
                'deposit_room_2',
                'deposit_room_3',
                'deposit_room_4',
                'disabled_access',
                'garage',
                'garden_patio',
                'gender',
                'living_room',
                'max_age',
                'min_age',
                'number_housemates',
                'number_flatmates',
                'occupation',
                'pets_allowed',
                'references',
                'room_type',
                'smalloweding_allowed',
                'total_number_rooms',
                'vegetarian_vegan'
            ]);
        });
    }
};
