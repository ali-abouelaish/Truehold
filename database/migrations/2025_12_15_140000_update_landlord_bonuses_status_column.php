<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

        // Some older databases may have `status` as an ENUM that does not include "sent"
        // or as a numeric column. This migration normalises it to a VARCHAR so values
        // like "pending", "sent", "paid", "cancelled" all work without truncation.
        try {
            DB::statement("
                ALTER TABLE landlord_bonuses
                MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending'
            ");
        } catch (\Throwable $e) {
            // If the column doesn't exist or the DB engine doesn't support this exact SQL,
            // just log it – we don't want the whole migration run to crash.
            \Log::error('Failed to alter landlord_bonuses.status column', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't attempt to revert the column type automatically,
        // as we don't know the original enum definition on every install.
    }
};


