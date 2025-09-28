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
        Schema::table('clients', function (Blueprint $table) {
            $table->decimal('budget', 10, 2)->nullable()->after('position_role');
            $table->string('area_of_interest')->nullable()->after('budget');
            $table->date('moving_date')->nullable()->after('area_of_interest');
            $table->text('notes')->nullable()->after('moving_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['budget', 'area_of_interest', 'moving_date', 'notes']);
        });
    }
};
