<?php
namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Workshop;
use App\Notifications\NewInspectionRequestNotification; 
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification; 
use Tests\TestCase;

class InspectionRequestTest extends TestCase
{
    use RefreshDatabase; 

   
    public function test_authenticated_user_can_request_an_inspection(): void
    {
        // 1. Setup
        Notification::fake(); 

        $user = User::factory()->create();
        $workshop = Workshop::factory()->create();
        $workshopOwner = User::factory()->create();
        $workshop->user_id = $workshopOwner->id;
        $workshop->save();
     


        $requestData = [
            'workshop_id' => $workshop->id,
            'preferred_datetime' => now()->addDays(3)->format('Y-m-d H:i:s'), // تاريخ مستقبلي
            'notes' => 'فحص شامل للفرامل والمحرك.',
        ];

        // 2. Action
        $response = $this->actingAs($user, 'sanctum') // المصادقة كمستخدم
                       ->postJson(route('inspection-requests.store'), $requestData); // إرسال الطلب

        // 3. Assertions
        $response->assertStatus(201) // التأكد من حالة HTTP 201 Created
                 ->assertJsonStructure([ // التأكد من بنية الـ JSON المُرجع
                     'message',
                     'data' => [
                         'id',
                         'user_id',
                         'workshop_id',
                         'preferred_datetime',
                         'notes',
                         'status',
                         'created_at',
                         'updated_at',
                     ]
                 ])
                 ->assertJsonFragment([ // التأكد من بعض القيم الصحيحة
                     'user_id' => $user->id,
                     'workshop_id' => $workshop->id,
                     'notes' => $requestData['notes'],
                     'status' => 'pending',
                 ]);

        // التأكد من تخزين الطلب في قاعدة البيانات
        $this->assertDatabaseHas('inspection_requests', [
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'notes' => $requestData['notes'],
            'status' => 'pending',
            // لا تقارن preferred_datetime مباشرة كنص إذا كان هناك تحويل للمنطقة الزمنية
        ]);

        // التأكد من إرسال الإشعار للورشة (أو مالكها)
         // استبدل $workshop أو $workshopOwner بالمتلقي الفعلي للإشعار
         $recipient = $workshop; // أو $workshop->owner حسب المنطق في Service
        Notification::assertSentTo(
            [$recipient], // يجب أن يكون iterable (مصفوفة)
            NewInspectionRequestNotification::class,
            function ($notification, $channels) use ($workshop) {
                // يمكنك إضافة تحقق إضافي هنا على محتوى الإشعار إذا أردت
                // $notification->inspectionRequest يحتوي على الطلب
                return $notification->inspectionRequest->workshop_id === $workshop->id;
            }
        );
    }

    public function test_user_can_delete_own_inspection_request(): void
    {
        // Fake notifications
        Notification::fake();
    
        // إعداد المستخدم والورشة
        $user = User::factory()->create();
        $workshop = Workshop::factory()->create();
    
        // إنشاء طلب فحص
        $inspectionRequest = $user->inspectionRequest()->create([
            'workshop_id' => $workshop->id,
            'preferred_datetime' => now()->addDays(2),
            'notes' => 'ملاحظة اختبار',
            'status' => 'pending',
        ]);
    
        // تنفيذ الحذف كمستخدم مصادق عليه
        $response = $this->actingAs($user, 'sanctum')
                         ->deleteJson(route('inspection-requests.destroy', $inspectionRequest->id));
    
        // التأكد من الحذف والرد
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Inspection request deleted successfully.',
                 ]);
    
        // تأكد أن الطلب لم يعد موجودًا
        $this->assertDatabaseMissing('inspection_requests', [
            'id' => $inspectionRequest->id,
        ]);
    }
   
   
}