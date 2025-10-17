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
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('agent_id');
            $table->string('landlord')->nullable();
            $table->string('property')->nullable();
            $table->string('client')->nullable();
            $table->decimal('full_commission', 10, 2);
            $table->decimal('agent_commission', 10, 2);
            $table->boolean('invoice_sent_to_management')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonuses');
    }
};
