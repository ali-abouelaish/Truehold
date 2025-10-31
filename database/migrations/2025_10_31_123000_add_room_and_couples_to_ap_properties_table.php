<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ap_properties', function (Blueprint $table) {
            $table->boolean('is_room')->default(false)->after('status');
            $table->boolean('couples_allowed')->default(false)->after('is_room');
        });
    }

    public function down(): void
    {
        Schema::table('ap_properties', function (Blueprint $table) {
            $table->dropColumn(['is_room', 'couples_allowed']);
        });
    }
};


