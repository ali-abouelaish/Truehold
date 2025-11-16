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
        if (! Schema::hasTable('landlord_bonuses')) {
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
            return;
        }

        Schema::table('landlord_bonuses', function (Blueprint $table) {
            if (! Schema::hasColumn('landlord_bonuses', 'status')) {
                $table->string('status')->default('pending')->after('agency_commission');
            }
            if (! Schema::hasColumn('landlord_bonuses', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
            if (! Schema::hasColumn('landlord_bonuses', 'bonus_code')) {
                $table->string('bonus_code')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('landlord_bonuses', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('bonus_code')->constrained('users');
            }
        });

        if (! Schema::hasColumn('landlord_bonuses', 'bonus_split')) {
            Schema::table('landlord_bonuses', function (Blueprint $table) {
                $table->string('bonus_split')->default('55_45')->after('commission');
            });
        }

        Schema::table('landlord_bonuses', function (Blueprint $table) {
            if (! Schema::hasColumn('landlord_bonuses', 'agent_commission')) {
                $table->decimal('agent_commission', 10, 2)->default(0)->after('bonus_split');
            }
            if (! Schema::hasColumn('landlord_bonuses', 'agency_commission')) {
                $table->decimal('agency_commission', 10, 2)->default(0)->after('agent_commission');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank. This migration is a schema fix.
    }
};







