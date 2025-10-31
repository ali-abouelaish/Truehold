<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ap_properties', function (Blueprint $table) {
            $table->boolean('is_room')->default(false)->after('is_house_share');
            $table->boolean('couples_allowed')->default(false)->after('is_room');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ap_properties', function (Blueprint $table) {
            $table->dropColumn(['is_room', 'couples_allowed']);
        });
    }
};


