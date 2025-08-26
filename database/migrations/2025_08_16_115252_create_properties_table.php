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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('url')->nullable();
            $table->string('title')->nullable();
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('price')->nullable();
            $table->text('description')->nullable();
            $table->string('property_type')->nullable();
            $table->string('available_date')->nullable();
            $table->integer('photo_count')->default(0);
            $table->string('first_photo_url')->nullable();
            $table->text('all_photos')->nullable();
            $table->json('photos')->nullable();
            $table->string('contact_info')->nullable();
            $table->string('management_company')->nullable();
            $table->text('amenities')->nullable();
            
            // Additional fields from the detailed scraping
            $table->string('age')->nullable();
            $table->string('ages')->nullable();
            $table->string('any_pets')->nullable();
            $table->string('available')->nullable();
            $table->string('balcony_roof_terrace')->nullable();
            $table->string('bills_included')->nullable();
            $table->string('broadband_included')->nullable();
            $table->string('couples_allowed')->nullable();
            $table->string('deposit')->nullable();
            $table->string('deposit_room_1')->nullable();
            $table->string('deposit_room_2')->nullable();
            $table->string('deposit_room_3')->nullable();
            $table->string('deposit_room_4')->nullable();
            $table->string('disabled_access')->nullable();
            $table->string('furnishings')->nullable();
            $table->string('garage')->nullable();
            $table->string('garden_patio')->nullable();
            $table->string('gender')->nullable();
            $table->string('living_room')->nullable();
            $table->string('max_age')->nullable();
            $table->string('maximum_term')->nullable();
            $table->string('min_age')->nullable();
            $table->string('minimum_term')->nullable();
            $table->string('number')->nullable();
            $table->string('status')->default('available');
            
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('location');
            $table->index('price');
            $table->index('property_type');
            $table->index('available_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
