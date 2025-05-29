<?php

namespace Tests\Unit\Services;

use App\Models\InspectionRequest;
use App\Models\User;
use App\Models\Workshop;
use App\Notifications\NewInspectionRequestNotification;
use App\Repositories\InspectionRequestRepository;
use App\Services\InspectionRequestService;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InspectionRequestServiceTest extends TestCase
{
    use RefreshDatabase;
    public function test_create_inspection_request_successfully()
    {

        Notification::fake(); // منع الإشعارات الحقيقية

        // بيانات الإدخال
        $validatedData = [
            'workshop_id' => 1,
            'preferred_datetime' => now()->addDay(),
            'notes' => 'Test note',
        ];

        // نموذج مستخدم وهمي
        $user = User::factory()->make(['id' => 99]);

        // نموذج الطلب المتوقع إنشاؤه
        $expectedInspectionRequest = new InspectionRequest(array_merge($validatedData, [
            'user_id' => $user->id,
            'status' => 'pending',
        ]));

        // تهيئة الـ Repository كمجسم وهمي
        $mockRepo = Mockery::mock(InspectionRequestRepository::class);
        $mockRepo->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) use ($user) {
                return $arg['user_id'] === $user->id &&
                       $arg['status'] === 'pending';
            }))
            ->andReturn($expectedInspectionRequest);

            $workshop = Workshop::factory()->create(['id' => 1]);

        // تهيئة الخدمة مع الـ Mock Repository
        $service = new InspectionRequestService($mockRepo);

        // تنفيذ الدالة
        $result = $service->createInspectionRequest($validatedData, $user);

        // تأكيد أن النتيجة هي نفس الطلب الذي أرجعه الـ Repository
        $this->assertEquals($expectedInspectionRequest, $result);

        // تأكيد إرسال إشعار للورشة
        Notification::assertSentTo(
            [$workshop],
            NewInspectionRequestNotification::class,
            function ($notification, $channels) use ($expectedInspectionRequest) {
                return $notification->inspectionRequest === $expectedInspectionRequest;
            }
        );
    }


    public function test_delete_inspection_request_not_found()
{
    $nonExistingId = 999;

    $mockRepo = Mockery::mock(InspectionRequestRepository::class);
    $mockRepo->shouldReceive('findById')
        ->once()
        ->with($nonExistingId)
        ->andReturn(null);

    // لا يتم استدعاء delete أصلاً لأن الطلب غير موجود

    $service = new InspectionRequestService($mockRepo);

    $result = $service->deleteInspectionRequest($nonExistingId);

    $this->assertFalse($result);
}

}
