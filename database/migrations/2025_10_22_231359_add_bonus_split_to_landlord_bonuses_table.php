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
        Schema::table('landlord_bonuses', function (Blueprint $table) {
            $table->enum('bonus_split', ['55_45', '100_0'])->default('55_45')->after('commission');
            $table->decimal('agent_commission', 10, 2)->after('bonus_split');
            $table->decimal('agency_commission', 10, 2)->after('agent_commission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landlord_bonuses', function (Blueprint $table) {
            $table->dropColumn(['bonus_split', 'agent_commission', 'agency_commission']);
        });
    }
};
