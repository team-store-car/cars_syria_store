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
        Schema::create('car_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->enum('offer_type', ['sale', 'rent']);
            $table->decimal('price', 10, 2);
            $table->string('price_unit')->default('SYR');
            $table->string('location');
            $table->string('pricing_period')->nullable();
            $table->boolean('is_available')->default(true);
            $table->text('additional_features')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_offers');
    }
};
