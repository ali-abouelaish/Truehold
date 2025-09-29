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
            if (!Schema::hasColumn('rental_codes', 'paid')) {
                $table->boolean('paid')->default(false)->after('status');
            }
            if (!Schema::hasColumn('rental_codes', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('paid');
            }
            if (!Schema::hasColumn('rental_codes', 'marketing_agent')) {
                $table->string('marketing_agent')->nullable()->after('rent_by_agent');
            }
            if (!Schema::hasColumn('rental_codes', 'client_count')) {
                $table->integer('client_count')->default(1)->after('client_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_codes', function (Blueprint $table) {
            $table->dropColumn(['paid', 'paid_at', 'marketing_agent', 'client_count']);
        });
    }
};
