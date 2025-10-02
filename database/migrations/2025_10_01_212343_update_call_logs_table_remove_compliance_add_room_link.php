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
        Schema::table('call_logs', function (Blueprint $table) {
            // Add room_link field
            $table->string('room_link')->nullable()->after('furnished');
            
            // Remove compliance fields
            $table->dropColumn([
                'works_pending',
                'compliance_epc',
                'compliance_eicr',
                'compliance_gas',
                'compliance_licence'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('call_logs', function (Blueprint $table) {
            // Remove room_link field
            $table->dropColumn('room_link');
            
            // Add back compliance fields
            $table->text('works_pending')->nullable()->after('furnished');
            $table->boolean('compliance_epc')->default(false)->after('works_pending');
            $table->boolean('compliance_eicr')->default(false)->after('compliance_epc');
            $table->boolean('compliance_gas')->default(false)->after('compliance_eicr');
            $table->boolean('compliance_licence')->default(false)->after('compliance_gas');
        });
    }
};