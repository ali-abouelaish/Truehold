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
        if (! Schema::hasTable('landlord_bonuses')) {
            return;
        }

        Schema::table('landlord_bonuses', function (Blueprint $table) {
            if (! Schema::hasColumn('landlord_bonuses', 'agent_commission')) {
                $table->decimal('agent_commission', 10, 2)->default(0)->after('commission');
            }
            if (! Schema::hasColumn('landlord_bonuses', 'agency_commission')) {
                $table->decimal('agency_commission', 10, 2)->default(0)->after('agent_commission');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('landlord_bonuses')) {
            return;
        }

        Schema::table('landlord_bonuses', function (Blueprint $table) {
            if (Schema::hasColumn('landlord_bonuses', 'agency_commission')) {
                $table->dropColumn('agency_commission');
            }
            if (Schema::hasColumn('landlord_bonuses', 'agent_commission')) {
                $table->dropColumn('agent_commission');
            }
        });
    }
};
