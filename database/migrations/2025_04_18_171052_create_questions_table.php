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
        Schema::create('questions', function (Blueprint $table) {
            $table->id(); // مفتاح أساسي (Primary Key) bigint unsigned auto-increment
            $table->text('text'); // نص السؤال (استخدم text للسماح بنصوص طويلة)
            // يمكنك إضافة أعمدة أخرى اختيارية هنا حسب الحاجة:
            // $table->string('identifier')->unique()->nullable(); // معرّف فريد للسؤال إذا لزم الأمر
            // $table->integer('order')->default(0); // لترتيب ظهور الأسئلة
            // $table->boolean('is_active')->default(true); // لتفعيل أو تعطيل السؤال
            $table->timestamps(); // يُنشئ created_at و updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};