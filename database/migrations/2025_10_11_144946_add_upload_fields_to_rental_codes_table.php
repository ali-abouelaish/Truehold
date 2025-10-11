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
            // Client document upload fields
            $table->string('client_contract')->nullable()->after('notes')->comment('Client contract document');
            $table->string('payment_proof')->nullable()->after('client_contract')->comment('Payment proof document');
            $table->string('client_id_document')->nullable()->after('payment_proof')->comment('Client ID document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_codes', function (Blueprint $table) {
            $table->dropColumn([
                'client_contract',
                'payment_proof', 
                'client_id_document'
            ]);
        });
    }
};