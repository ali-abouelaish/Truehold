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
            if (!Schema::hasColumn('clients', 'marketing_agent_id')) {
                $table->unsignedBigInteger('marketing_agent_id')->nullable()->after('agent_id');
                $table->foreign('marketing_agent_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'marketing_agent_id')) {
                $table->dropForeign(['marketing_agent_id']);
                $table->dropColumn('marketing_agent_id');
            }
        });
    }
};