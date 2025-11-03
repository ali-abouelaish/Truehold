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
        if (Schema::hasTable('landlord_bonuses')) {
            return;
        }

        Schema::create('landlord_bonuses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('agent_id')->constrained('agents');
            $table->string('landlord');
            $table->string('property');
            $table->string('client');
            $table->decimal('commission', 10, 2)->default(0);
            $table->string('bonus_split')->default('55_45');
            $table->decimal('agent_commission', 10, 2)->default(0);
            $table->decimal('agency_commission', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->string('bonus_code')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
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

        Schema::dropIfExists('landlord_bonuses');
    }
};
