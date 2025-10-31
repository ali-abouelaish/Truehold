<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ap_properties', function (Blueprint $table) {
            $table->id();
            $table->date('availability')->nullable();
            $table->string('property_name');
            $table->integer('pcm')->nullable();
            $table->string('postcode')->nullable();
            $table->string('area')->nullable();
            $table->integer('n_rooms')->default(0);
            $table->integer('n_bathrooms')->default(0);
            $table->json('images_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ap_properties');
    }
};


