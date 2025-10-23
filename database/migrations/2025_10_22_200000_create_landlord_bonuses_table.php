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
        Schema::create('landlord_bonuses', function (Blueprint $table) {
            $table->id();
            $table->string('bonus_code')->unique();
            $table->date('date');
            $table->unsignedBigInteger('agent_id');
            $table->string('landlord');
            $table->string('property');
            $table->string('client');
            $table->decimal('commission', 10, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landlord_bonuses');
    }
};
