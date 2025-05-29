<?php

namespace Tests\Unit;

use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_middleware_allows_access_for_valid_role()
    {
        // إعداد البيئة
        $role = Role::firstOrCreate(['name' => 'workshop', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('workshop');
        $this->actingAs($user);

        // تعريف المسار مع الـ Middleware
        Route::get('/test-route', function () {
            return 'OK';
        })->middleware('role:workshop');

        // إرسال الطلب
        $response = $this->get('/test-route');

        // التحقق من النتيجة
        $response->assertStatus(200);
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_role_middleware_denies_access_for_invalid_role()
    {
        // إنشاء مستخدم بدون دور 'workshop'
        $user = User::factory()->create();
        $this->actingAs($user);

        // تعريف المسار مع الـ Middleware
        Route::get('/test-route', function () {
            return 'OK';
        })->middleware('role:workshop');

        // إرسال الطلب
        $response = $this->get('/test-route');

        // التحقق من رفض الوصول
        $response->assertStatus(403);
        $response->assertJson(['message' => 'Access Denied']);
    }
}