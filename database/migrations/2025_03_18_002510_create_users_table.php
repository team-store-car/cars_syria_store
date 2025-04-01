<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // مفتاح أساسي (Primary Key)
            $table->string('name'); // اسم المستخدم
            $table->string('email')->unique(); // البريد الإلكتروني (فريد)
            $table->string('password'); // كلمة المرور (يتم تخزينها مشفرة)
            $table->string('password_confirmation')->nullable(); // تأكيد كلمة المرور (اختياري في قاعدة البيانات)
            $table->enum('role', ['admin', 'user', 'workshop', 'shop_manager'])->default('user'); // دور المستخدم
            $table->string('phone')->nullable(); // رقم الهاتف (اختياري)
            $table->string('avatar')->nullable(); // صورة الملف الشخصي (اختياري)
            $table->rememberToken(); // تذكر المستخدم عند تسجيل الدخول
            $table->timestamps(); // `created_at` و `updated_at`
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users'); // حذف الجدول عند التراجع
    }
};
