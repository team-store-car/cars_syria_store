<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase; // لإعادة تعيين قاعدة البيانات بعد كل اختبار

    public function test_login_with_valid_credentials()
    {
        // إنشاء مستخدم لاختباره
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // إرسال طلب تسجيل الدخول
        $response = $this->postJson('/api/login', [
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);

        // التحقق من أن الاستجابة تحتوي على بيانات صحيحة
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'user',
                     'token'
                 ]);
    }

    public function test_login_with_invalid_credentials()
    {
        // إنشاء مستخدم لاختباره
        User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // إرسال بيانات غير صحيحة
        $response = $this->postJson('/api/login', [
            'email'    => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        // يجب أن يرجع خطأ 401
        $response->assertStatus(401)
                 ->assertJson(['message' => 'بيانات تسجيل الدخول غير صحيحة']);
    }

    public function test_login_with_missing_fields()
    {
        // إرسال طلب بدون بيانات
        $response = $this->postJson('/api/login', []);

        // يجب أن يرجع خطأ 422 بسبب فشل التحقق
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email', 'password']);
    }
}
