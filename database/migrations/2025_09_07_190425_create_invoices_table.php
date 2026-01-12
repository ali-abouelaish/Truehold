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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->string('payment_terms')->default('Net 7');
            $table->string('po_number')->nullable();
            
            // Company information
            $table->string('company_name');
            $table->text('company_address');
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_website')->nullable();
            
            // Banking details
            $table->string('account_holder_name');
            $table->string('account_number');
            $table->string('sort_code');
            $table->string('bank_name')->nullable();
            
            // Client information
            $table->string('client_name');
            $table->text('client_address');
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            
            // Invoice items (stored as JSON)
            $table->json('items');
            
            // Financial details
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('balance_due', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            
            // Status and notes
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
