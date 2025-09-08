<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_viewing_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_viewing_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['invited', 'confirmed', 'declined', 'attended', 'no_show'])->default('invited');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['group_viewing_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_viewing_attendees');
    }
};


