<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ap_properties', function (Blueprint $table) {
            $table->string('room_label', 100)->nullable()->after('is_room');
        });
    }

    public function down(): void
    {
        Schema::table('ap_properties', function (Blueprint $table) {
            $table->dropColumn('room_label');
        });
    }
};


