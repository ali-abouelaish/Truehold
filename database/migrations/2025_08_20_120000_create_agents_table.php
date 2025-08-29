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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('agent_code')->unique()->nullable(); // Unique agent identifier
            $table->string('company_name')->nullable(); // Agency/company name
            $table->string('license_number')->nullable(); // Professional license number
            $table->string('phone')->nullable(); // Direct phone number
            $table->string('mobile')->nullable(); // Mobile number
            $table->string('website')->nullable(); // Personal/company website
            $table->text('bio')->nullable(); // Professional biography
            $table->string('specialization')->nullable(); // Property type specialization
            $table->string('experience_years')->nullable(); // Years of experience
            $table->string('languages')->nullable(); // Languages spoken
            $table->string('office_address')->nullable(); // Office address
            $table->string('office_city')->nullable(); // Office city
            $table->string('office_postcode')->nullable(); // Office postcode
            $table->string('office_phone')->nullable(); // Office phone
            $table->string('office_email')->nullable(); // Office email
            $table->string('profile_photo')->nullable(); // Profile photo URL
            $table->json('social_media')->nullable(); // Social media links
            $table->json('certifications')->nullable(); // Professional certifications
            $table->json('awards')->nullable(); // Awards and recognition
            $table->string('working_hours')->nullable(); // Working hours
            $table->boolean('is_verified')->default(false); // Verification status
            $table->boolean('is_featured')->default(false); // Featured agent status
            $table->integer('properties_count')->default(0); // Number of properties managed
            $table->decimal('rating', 3, 2)->nullable(); // Agent rating (0.00 - 5.00)
            $table->integer('reviews_count')->default(0); // Number of reviews
            $table->timestamp('last_active')->nullable(); // Last activity timestamp
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id']);
            $table->index(['agent_code']);
            $table->index(['company_name']);
            $table->index(['is_verified']);
            $table->index(['is_featured']);
            $table->index(['rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
