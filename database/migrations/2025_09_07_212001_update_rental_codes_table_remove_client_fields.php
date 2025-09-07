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
            // Remove client fields since they're now stored in the clients table
            $table->dropColumn([
                'client_full_name',
                'client_date_of_birth',
                'client_phone_number',
                'client_email',
                'client_nationality',
                'client_current_address',
                'client_company_university_name',
                'client_company_university_address',
                'client_position_role'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_codes', function (Blueprint $table) {
            // Add back client fields
            $table->string('client_full_name');
            $table->date('client_date_of_birth');
            $table->string('client_phone_number');
            $table->string('client_email');
            $table->string('client_nationality');
            $table->text('client_current_address');
            $table->string('client_company_university_name')->nullable();
            $table->text('client_company_university_address')->nullable();
            $table->string('client_position_role')->nullable();
        });
    }
};