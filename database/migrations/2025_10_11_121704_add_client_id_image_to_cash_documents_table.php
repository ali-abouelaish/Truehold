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
        // Only add the column if it doesn't already exist to avoid duplicate column errors
        if (! Schema::hasColumn('cash_documents', 'client_id_image')) {
            Schema::table('cash_documents', function (Blueprint $table) {
                $table->string('client_id_image')->nullable()->after('contact_images');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('cash_documents', 'client_id_image')) {
            Schema::table('cash_documents', function (Blueprint $table) {
                $table->dropColumn('client_id_image');
            });
        }
    }
};
