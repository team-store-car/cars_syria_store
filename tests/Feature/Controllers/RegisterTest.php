<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase; 

    /**
     * اختبار إمكانية تسجيل مستخدم جديد بنجاح ببيانات صالحة.
     * @test
     */
    public function user_can_register_with_valid_data(): void
    {   
         $this->seed(\Database\Seeders\RoleSeeder::class);

        $userData = [
            'name'                  => 'Test User',
            'email'                 => 'testregister@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123', 
            'role' => 'user', 
        ];

        // 2. إرسال طلب POST إلى مسار التسجيل
        // المسار '/auth/register' بناءً على الإعدادات السابقة في bootstrap/app.php و routes/auth.php
        $response = $this->postJson('/auth/register', $userData);

        // 3. التأكيد على نجاح العملية (Assertions)

        // التأكد من أن حالة الاستجابة هي 201 Created
        $response->assertStatus(201);

        // التأكد من أن بنية JSON المستلمة تحتوي على المفاتيح المتوقعة
        $response->assertJsonStructure([
            'message',
            'user' => [ // التأكد من بنية بيانات المستخدم إذا أردت
                'id',
                'name',
                'email',
                // 'created_at', // وغيرها من الحقول التي تتوقعها
                // 'updated_at',
            ],
            'token',
        ]);

        // التأكد من أن رسالة النجاح مطابقة
        $response->assertJsonFragment(['message' => 'تم تسجيل الحساب بنجاح']);

        // التأكد من أن المستخدم تم إنشاؤه بالفعل في قاعدة البيانات
        $this->assertDatabaseHas('users', [
            'email' => 'testregister@example.com',
            'name'  => 'Test User',
        ]);

        // التأكد من أن كلمة المرور المحفوظة في قاعدة البيانات ليست النص العادي
        // (اختياري لكن جيد للتحقق من التشفير)
        $user = User::where('email', 'testregister@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertNotEquals('password123', $user->password); // تأكيد إضافي أنها ليست نص عادي
    }

    /**
     * اختبار فشل التسجيل عند عدم تطابق كلمتي المرور.
     * @test
     */
    public function registration_fails_if_passwords_do_not_match(): void
    {
        $userData = [
            'name'                  => 'Test User',
            'email'                 => 'testmismatch@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password456', // كلمة مرور غير متطابقة
        ];

        // نتوقع فشل التحقق من الصحة (Validation)
        $response = $this->postJson('/auth/register', $userData);

        // التأكد من أن حالة الاستجابة هي 422 Unprocessable Entity
        $response->assertStatus(422);

        // التأكد من وجود خطأ في حقل كلمة المرور (الاسم الدقيق للخطأ يعتمد على RegisterRequest)
        $response->assertJsonValidationErrors('password');

        // التأكد من عدم إنشاء المستخدم في قاعدة البيانات
        $this->assertDatabaseMissing('users', [
            'email' => 'testmismatch@example.com',
        ]);
    }

    /**
     * اختبار فشل التسجيل عند استخدام بريد إلكتروني موجود مسبقًا.
     * @test
     */
    public function registration_fails_if_email_already_exists(): void
    {
        // 1. إنشاء مستخدم أولاً بنفس البريد الإلكتروني
        User::factory()->create(['email' => 'existing@example.com']);

        // 2. محاولة التسجيل بنفس البريد الإلكتروني
        $userData = [
            'name'                  => 'Another User',
            'email'                 => 'existing@example.com', // بريد موجود مسبقًا
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/auth/register', $userData);

        // التأكد من حالة الفشل 422
        $response->assertStatus(422);

        // التأكد من وجود خطأ في حقل البريد الإلكتروني
        $response->assertJsonValidationErrors('email');
    }

        /**
     * اختبار فشل التسجيل عند ترك حقول مطلوبة فارغة.
     * @test
     */
    public function registration_fails_if_required_fields_are_missing(): void
    {
        $userData = [
            // 'name' => 'Test User', // حقل الاسم مفقود
            'email'                 => 'missingfields@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
   

        ];

        $response = $this->postJson('/auth/register', $userData);

        // التأكد من حالة الفشل 422
        $response->assertStatus(422);

        // التأكد من وجود خطأ في الحقل المفقود (الاسم)
        $response->assertJsonValidationErrors('name');
         // يمكنك إضافة تأكيدات لحقول أخرى إذا كانت مطلوبة ولم يتم إرسالها
        // $response->assertJsonMissingValidationErrors('email'); // تأكيد عدم وجود خطأ في حقل البريد لأنه موجود

    }

}