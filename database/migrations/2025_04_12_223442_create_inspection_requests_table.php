<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // المستخدم الذي قدم الطلب
            $table->foreignId('workshop_id')->constrained()->onDelete('cascade'); // الورشة المختارة
            $table->dateTime('preferred_datetime'); // الموعد المفضل من قبل المستخدم
            $table->text('notes')->nullable(); // ملاحظات إضافية من المستخدم
            $table->string('status')->default('pending'); // حالة الطلب (pending, scheduled, completed, cancelled)
            // يمكن إضافة حقول أخرى مثل تفاصيل السيارة إذا لم تكن مرتبطة بالـ user مباشرة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_requests');
    }
};