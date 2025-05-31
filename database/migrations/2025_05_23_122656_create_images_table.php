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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->morphs('imageable'); // Polymorphic relationship (imageable_id, imageable_type)
            $table->string('path'); // File path in storage
            $table->string('alt_text')->nullable(); // Optional alt text for accessibility
            $table->boolean('is_primary')->default(false); // Flag for primary image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
