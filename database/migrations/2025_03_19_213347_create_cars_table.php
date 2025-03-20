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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Car Name
            $table->string('brand'); // Brand Name
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Car Category
            $table->string('country_of_manufacture'); // Manufacturing Country
            $table->string('model'); // Model Name
            $table->year('year'); // Year of Manufacture
            $table->enum('condition', ['new', 'used']); // Car Condition (New / Used)
            $table->integer('mileage')->nullable(); // Mileage (for used cars)
            $table->string('fuel_type'); // Fuel Type (Petrol, Diesel, Electric...)
            $table->string('transmission'); // Transmission Type (Automatic / Manual)
            $table->integer('horsepower')->nullable(); // Engine Power (HP)
            $table->integer('seats'); // Number of Seats
            $table->string('color'); // Car Color
            $table->text('description')->nullable(); // Car Description
            $table->boolean('is_featured')->default(false); // Is Featured?
            $table->text('other_benefits')->nullable(); // Other Benefits
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
