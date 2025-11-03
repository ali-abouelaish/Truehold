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

        if (! Schema::hasColumn('landlord_bonuses', 'bonus_code')) {
            Schema::table('landlord_bonuses', function (Blueprint $table) {
                $table->string('bonus_code')->nullable()->after('notes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('landlord_bonuses')) {
            return;
        }

        if (Schema::hasColumn('landlord_bonuses', 'bonus_code')) {
            Schema::table('landlord_bonuses', function (Blueprint $table) {
                $table->dropColumn('bonus_code');
            });
        }
    }
};
