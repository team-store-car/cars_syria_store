<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->string('name'); 
            $table->string('commercial_registration_number')->unique()->nullable();
            $table->string('commercial_registration_image')->nullable(); 
            $table->string('location');
            $table->string('city')->nullable();
            $table->text('description')->nullable();
            $table->text('certification_details')->nullable();
            $table->boolean('verified')->default(false); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('workshops');
    }
};