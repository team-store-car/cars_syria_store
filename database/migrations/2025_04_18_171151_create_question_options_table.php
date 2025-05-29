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
        Schema::create('question_options', function (Blueprint $table) {
            $table->id(); // مفتاح أساسي
            // تعريف المفتاح الأجنبي (Foreign Key) لربطه بجدول questions
            $table->foreignId('question_id') // يُنشئ عمود bigint unsigned
                  ->constrained('questions') // يحدد أنه مرتبط بجدول questions وعمود id فيه
                  ->onDelete('cascade'); // لحذف الخيارات تلقائياً عند حذف السؤال المرتبط بها (اختياري لكن موصى به)

            $table->string('text'); // نص الخيار المعروض للمستخدم
            // يمكنك إضافة أعمدة اختيارية أخرى:
            // $table->string('value')->nullable(); // قيمة داخلية للخيار إذا كانت مختلفة عن النص أو الـ ID
            // $table->integer('order')->default(0); // لترتيب ظهور الخيارات للسؤال الواحد
            // $table->boolean('is_active')->default(true); // لتفعيل أو تعطيل الخيار
            $table->timestamps(); // created_at و updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};