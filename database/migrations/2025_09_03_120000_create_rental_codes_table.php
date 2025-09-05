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
        Schema::create('rental_codes', function (Blueprint $table) {
            $table->id();
            $table->string('rental_code')->unique();
            $table->date('rental_date');
            $table->decimal('consultation_fee', 10, 2);
            $table->string('payment_method');
            $table->string('property')->nullable();
            $table->string('licensor')->nullable();
            
            // Client Information
            $table->string('client_full_name');
            $table->date('client_date_of_birth');
            $table->string('client_phone_number');
            $table->string('client_email');
            $table->string('client_nationality');
            $table->text('client_current_address');
            $table->string('client_company_university_name')->nullable();
            $table->text('client_company_university_address')->nullable();
            $table->string('client_position_role')->nullable();
            
            // Agent Information
            $table->string('rent_by_agent');
            $table->string('client_by_agent');
            
            // Additional fields
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, approved, completed, cancelled
            
            $table->timestamps();
            
            // Add indexes
            $table->index('rental_code');
            $table->index('rental_date');
            $table->index('status');
            $table->index('client_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_codes');
    }
};
