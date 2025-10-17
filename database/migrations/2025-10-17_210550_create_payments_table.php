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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('agent_id');
            $table->string('landlord')->nullable();
            $table->string('property')->nullable();
            $table->string('client')->nullable();
            $table->decimal('full_commission', 10, 2);
            $table->decimal('agent_commission', 10, 2);
            $table->enum('type', ['bonus', 'letting_deal', 'renewal', 'marketing', 'referral', 'other'])->default('bonus');
            $table->boolean('invoice_sent_to_management')->default(false);
            $table->enum('payment_status', ['unpaid', 'paid', 'rolled'])->default('unpaid');
            $table->enum('payment_method', ['transfer', 'cash', 'roll_to_next_month'])->nullable();
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->boolean('is_readonly')->default(false); // Becomes true when marked as paid
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            $table->index(['agent_id', 'date']);
            $table->index(['payment_status', 'date']);
            $table->index(['type', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
