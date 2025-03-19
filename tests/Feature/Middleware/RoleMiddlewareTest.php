<?php

use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    public function test_access_denied_if_user_not_authenticated()
    {
        // إرسال طلب بدون مستخدم مسجل
        $response = $this->get('/protected-route');

        // التحقق من أن الاستجابة 403 (Access Denied)
        $response->assertStatus(403)
                 ->assertJson(['message' => 'Access Denied']);
    }

    public function test_access_denied_if_user_does_not_have_role()
    {
        // إنشاء مستخدم بدون الدور المطلوب
        $user = User::factory()->create();

        // تسجيل دخول المستخدم
        $this->actingAs($user);

        // إرسال طلب إلى المسار المحمي
        $response = $this->get('/protected-route');

        // يجب أن يكون الوصول مرفوضًا
        $response->assertStatus(403)
                 ->assertJson(['message' => 'Access Denied']);
    }

    public function test_access_granted_if_user_has_required_role()
    {
        // إنشاء مستخدم لديه الدور المطلوب
        $user = User::factory()->create();

        // افتراض أن هناك دالة `hasRole` تعيد true لهذا المستخدم
        $user->assignRole('admin');

        // تسجيل دخول المستخدم
        $this->actingAs($user);

        // إرسال طلب إلى المسار المحمي
        $response = $this->get('/protected-route');

        // يجب أن يتم السماح بالوصول
        $response->assertStatus(200);
    }

    protected function setUp(): void
    {
    
        parent::setUp();
       
        // تعريف مسار تجريبي لحماية بالميدلوير
        Route::middleware(['role:admin'])->get('/protected-route', function () {
            return response()->json(['message' => 'Access Granted']);
        });
     

    }
  
}
