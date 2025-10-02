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
        Schema::table('call_logs', function (Blueprint $table) {
            // Make all fields nullable
            $table->string('call_type')->nullable()->change();
            $table->string('call_status')->nullable()->change();
            $table->datetime('call_datetime')->nullable()->change();
            $table->string('landlord_name')->nullable()->change();
            $table->string('landlord_phone')->nullable()->change();
            $table->string('landlord_email')->nullable()->change();
            $table->string('landlord_company')->nullable()->change();
            $table->string('contact_source')->nullable()->change();
            $table->text('property_address')->nullable()->change();
            $table->string('property_type')->nullable()->change();
            $table->integer('number_of_beds')->nullable()->change();
            $table->integer('number_of_bathrooms')->nullable()->change();
            $table->decimal('advertised_rent', 10, 2)->nullable()->change();
            $table->date('availability_date')->nullable()->change();
            $table->string('furnished')->nullable()->change();
            $table->text('room_link')->nullable()->change();
            $table->string('landlord_priority')->nullable()->change();
            $table->text('discovery_notes')->nullable()->change();
            $table->text('landlord_preference')->nullable()->change();
            $table->datetime('viewing_datetime')->nullable()->change();
            $table->datetime('follow_up_datetime')->nullable()->change();
            $table->string('next_step_status')->nullable()->change();
            $table->string('call_outcome')->nullable()->change();
            $table->text('agent_notes')->nullable()->change();
            $table->unsignedBigInteger('agent_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('call_logs', function (Blueprint $table) {
            // Reverse the nullable changes (make fields required again)
            $table->string('call_type')->nullable(false)->change();
            $table->string('call_status')->nullable(false)->change();
            $table->datetime('call_datetime')->nullable(false)->change();
            $table->string('landlord_name')->nullable(false)->change();
            $table->string('landlord_phone')->nullable(false)->change();
            $table->string('landlord_email')->nullable(false)->change();
            $table->string('landlord_company')->nullable(false)->change();
            $table->string('contact_source')->nullable(false)->change();
            $table->text('property_address')->nullable(false)->change();
            $table->string('property_type')->nullable(false)->change();
            $table->integer('number_of_beds')->nullable(false)->change();
            $table->integer('number_of_bathrooms')->nullable(false)->change();
            $table->decimal('advertised_rent', 10, 2)->nullable(false)->change();
            $table->date('availability_date')->nullable(false)->change();
            $table->string('furnished')->nullable(false)->change();
            $table->text('room_link')->nullable(false)->change();
            $table->string('landlord_priority')->nullable(false)->change();
            $table->text('discovery_notes')->nullable(false)->change();
            $table->text('landlord_preference')->nullable(false)->change();
            $table->datetime('viewing_datetime')->nullable(false)->change();
            $table->datetime('follow_up_datetime')->nullable(false)->change();
            $table->string('next_step_status')->nullable(false)->change();
            $table->string('call_outcome')->nullable(false)->change();
            $table->text('agent_notes')->nullable(false)->change();
            $table->unsignedBigInteger('agent_id')->nullable(false)->change();
        });
    }
};
