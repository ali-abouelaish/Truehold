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
            // Add columns only if they don't exist
            if (!Schema::hasColumn('rental_codes', 'rental_code')) {
                $table->string('rental_code')->unique()->after('id');
            }
            if (!Schema::hasColumn('rental_codes', 'rental_date')) {
                $table->date('rental_date')->after('rental_code');
            }
            if (!Schema::hasColumn('rental_codes', 'consultation_fee')) {
                $table->decimal('consultation_fee', 10, 2)->after('rental_date');
            }
            if (!Schema::hasColumn('rental_codes', 'payment_method')) {
                $table->string('payment_method')->after('consultation_fee');
            }
            if (!Schema::hasColumn('rental_codes', 'property')) {
                $table->string('property')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('rental_codes', 'licensor')) {
                $table->string('licensor')->nullable()->after('property');
            }
            if (!Schema::hasColumn('rental_codes', 'client_full_name')) {
                $table->string('client_full_name')->after('licensor');
            }
            if (!Schema::hasColumn('rental_codes', 'client_date_of_birth')) {
                $table->date('client_date_of_birth')->after('client_full_name');
            }
            if (!Schema::hasColumn('rental_codes', 'client_phone_number')) {
                $table->string('client_phone_number')->after('client_date_of_birth');
            }
            if (!Schema::hasColumn('rental_codes', 'client_email')) {
                $table->string('client_email')->after('client_phone_number');
            }
            if (!Schema::hasColumn('rental_codes', 'client_nationality')) {
                $table->string('client_nationality')->after('client_email');
            }
            if (!Schema::hasColumn('rental_codes', 'client_current_address')) {
                $table->text('client_current_address')->after('client_nationality');
            }
            if (!Schema::hasColumn('rental_codes', 'client_company_university_name')) {
                $table->string('client_company_university_name')->nullable()->after('client_current_address');
            }
            if (!Schema::hasColumn('rental_codes', 'client_company_university_address')) {
                $table->text('client_company_university_address')->nullable()->after('client_company_university_name');
            }
            if (!Schema::hasColumn('rental_codes', 'client_position_role')) {
                $table->string('client_position_role')->nullable()->after('client_company_university_address');
            }
            if (!Schema::hasColumn('rental_codes', 'rent_by_agent')) {
                $table->string('rent_by_agent')->after('client_position_role');
            }
            if (!Schema::hasColumn('rental_codes', 'client_by_agent')) {
                $table->string('client_by_agent')->after('rent_by_agent');
            }
            if (!Schema::hasColumn('rental_codes', 'notes')) {
                $table->text('notes')->nullable()->after('client_by_agent');
            }
            if (!Schema::hasColumn('rental_codes', 'status')) {
                $table->string('status')->default('pending')->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
