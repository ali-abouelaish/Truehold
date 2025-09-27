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
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            
            // Call Metadata
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->enum('call_type', ['outbound', 'inbound', 'follow_up', 'voicemail', 'sms_whatsapp']);
            $table->enum('call_status', ['connected', 'no_answer', 'wrong_number', 'voicemail', 'callback_requested']);
            $table->timestamp('call_datetime')->useCurrent();
            
            // Landlord Details
            $table->string('landlord_name');
            $table->string('landlord_phone')->nullable();
            $table->string('landlord_email')->nullable();
            $table->string('landlord_company')->nullable();
            $table->enum('contact_source', ['gumtree', 'spareroom', 'zoopla', 'rightmove', 'referral', 'other']);
            
            // Property Details
            $table->string('property_address');
            $table->enum('property_type', ['studio', 'one_bed', 'two_bed', 'hmo', 'other']);
            $table->decimal('advertised_rent', 10, 2);
            $table->date('availability_date')->nullable();
            $table->boolean('vacant_keys')->default(false);
            $table->enum('furnished', ['furnished', 'unfurnished', 'part_furnished', 'other']);
            
            // Discovery & Compliance
            $table->text('works_pending')->nullable();
            $table->boolean('compliance_epc')->default(false);
            $table->boolean('compliance_eicr')->default(false);
            $table->boolean('compliance_gas')->default(false);
            $table->boolean('compliance_licence')->default(false);
            $table->enum('landlord_priority', ['speed', 'best_price', 'hands_off', 'other']);
            $table->text('discovery_notes')->nullable();
            
            // Offer Presentation
            $table->json('packages_discussed')->nullable();
            $table->enum('landlord_preference', ['full_management', 'top_up', 'let_only', 'undecided']);
            
            // Objection Handling
            $table->json('objections')->nullable();
            $table->text('objection_response')->nullable();
            
            // Outcome & Next Steps
            $table->boolean('viewing_booked')->default(false);
            $table->timestamp('viewing_datetime')->nullable();
            $table->boolean('follow_up_needed')->default(false);
            $table->timestamp('follow_up_datetime')->nullable();
            $table->enum('next_step_status', ['send_terms', 'send_compliance_docs', 'awaiting_response', 'collect_keys', 'tenant_reference_started', 'other']);
            $table->enum('call_outcome', ['instruction_won', 'pending', 'lost', 'not_interested']);
            $table->text('agent_notes')->nullable();
            
            // Automation Hooks
            $table->boolean('send_sms')->default(false);
            $table->boolean('send_email')->default(false);
            $table->boolean('send_whatsapp')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_logs');
    }
};
