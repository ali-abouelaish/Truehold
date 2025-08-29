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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('full_name'); // Full Name
            $table->date('date_of_birth')->nullable(); // Date of Birth
            $table->string('phone_number')->nullable(); // Phone Number
            $table->string('email')->nullable(); // Email
            $table->string('nationality')->nullable(); // Nationality
            $table->text('current_address')->nullable(); // Current Address
            $table->string('company_university_name')->nullable(); // Company/University Name
            $table->text('company_university_address')->nullable(); // Company/University Address
            $table->string('position_role')->nullable(); // Position/Role
            $table->foreignId('agent_id')->nullable()->constrained()->onDelete('set null'); // Foreign key to agents table
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['full_name']);
            $table->index(['email']);
            $table->index(['phone_number']);
            $table->index(['agent_id']);
            $table->index(['nationality']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
