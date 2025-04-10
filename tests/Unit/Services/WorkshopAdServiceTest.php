<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Mockery;
use App\Models\User; // أضف هذا
use App\Models\Workshop;
use App\Models\WorkshopAd;
use App\Repositories\WorkshopAdRepository;
use App\Services\WorkshopAdService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use App\Helpers\AdHelper; // تأكد من وجود هذا إذا كان الفحص لا يزال في الخدمة
use PHPUnit\Framework\Attributes\Test;

// تأكد من استدعاء Mockery::close() في tearDown لتنظيف الموكات
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class WorkshopAdServiceTest extends TestCase
{
    // استخدم هذا الـ trait لتنظيف Mockery تلقائيًا
    use RefreshDatabase, MockeryPHPUnitIntegration;

    private WorkshopAdService $service;
    private $repositoryMock; // استخدم واجهة إذا كانت موجودة أو الكلاس نفسه

    protected function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = Mockery::mock(WorkshopAdRepository::class);
        $this->service = new WorkshopAdService($this->repositoryMock);
    
        // محاكاة AdHelper كـ Alias للدوال الثابتة
        Mockery::mock('alias:App\Helpers\AdHelper');
    }

    #[Test]
    public function it_creates_a_workshop_ad_when_limit_not_reached()
    {
        $workshop = Workshop::factory()->create();
        $data = ['title' => 'Test Ad', 'description' => 'Desc', 'price' => 100];
        $createdAd = new WorkshopAd($data + ['workshop_id' => $workshop->id]);
        // $createdAd->id = 1; // يمكنك تعيين ID وهمي إذا لزم الأمر

        // Mock AdHelper إذا كان الفحص موجودًا في الخدمة
         if (class_exists(AdHelper::class)) { // تحقق من وجود الكلاس لتجنب الخطأ إذا تم حذفه
            AdHelper::shouldReceive('hasReachedDailyLimit')
                ->once()
                ->with($workshop, 'workshopAds')
                ->andReturn(false); // افترض أن الحد لم يتم الوصول إليه
        }


        $this->repositoryMock
            ->shouldReceive('create')
            ->once()
            // تأكد من أن البيانات المرسلة للمستودع صحيحة
            ->with(Mockery::on(function ($arg) use ($data, $workshop) {
                return $arg['workshop_id'] === $workshop->id &&
                       $arg['title'] === $data['title'] &&
                       $arg['description'] === $data['description'] &&
                       $arg['price'] === $data['price'];
            }))
            ->andReturn($createdAd); // إرجاع الكائن الوهمي

        $response = $this->service->createWorkshopAd($data, $workshop);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals($data['title'], $responseData['title']); // تحقق من البيانات المرتجعة
    }

    #[Test]
    public function it_prevents_creating_ad_when_daily_limit_reached()
    {
        $workshop = Workshop::factory()->create();
        $data = ['title' => 'Test Ad', 'description' => 'Desc', 'price' => 100];

        // Mock AdHelper ليقول أن الحد قد تم الوصول إليه
        if (class_exists(AdHelper::class)) {
            AdHelper::shouldReceive('hasReachedDailyLimit')
                ->once()
                ->with($workshop, 'workshopAds')
                ->andReturn(true);
        }


        // تأكد من أن دالة create في المستودع لن يتم استدعاؤها
        $this->repositoryMock->shouldNotReceive('create');

        $response = $this->service->createWorkshopAd($data, $workshop);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode()); // Forbidden
        $this->assertEquals('لا يمكنك نشر أكثر من 3 إعلانات يومياً', $response->getData(true)['message']);
    }

    #[Test]
    public function it_updates_an_owned_workshop_ad()
    {
        $workshop = Workshop::factory()->create();
        // لا نستخدم factory هنا لأننا سنحصل على الكائن من route model binding
        $workshopAd = new WorkshopAd([
            'id' => 1,
            'workshop_id' => $workshop->id,
            'title' => 'Old Title',
            'description' => 'Old Desc',
            'price' => 50
        ]);
        // نحتاج إلى محاكاة وظيفة fresh() لأنها تُستخدم في الخدمة
        $workshopAdMock = Mockery::mock(WorkshopAd::class)->makePartial();
        $workshopAdMock->id = 1;
        $workshopAdMock->workshop_id = $workshop->id;


        $updateData = ['title' => 'New Title', 'price' => 150];

        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            // تأكد من أنه يستدعي التحديث على الكائن الصحيح وبالبيانات الصحيحة
            ->with(Mockery::on(function ($adArg) use ($workshopAd) {
                 // قد تحتاج إلى ضبط هذه المقارنة إذا كنت تمرر الكائن الفعلي
                 return $adArg->id === $workshopAd->id;
             }), $updateData)
            ->andReturn(true); // افترض أن التحديث نجح

         // محاكاة استدعاء fresh()
        $workshopAdMock
            ->shouldReceive('fresh')
            ->once()
            ->andReturn(new WorkshopAd($updateData + ['id' => 1, 'workshop_id' => $workshop->id])); // إرجاع كائن بالبيانات المحدثة


        // استدعاء الخدمة بالكائن الوهمي
        $response = $this->service->updateWorkshopAd($workshopAdMock, $updateData, $workshop);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode()); // OK
        $responseData = $response->getData(true);
        $this->assertEquals('New Title', $responseData['title']);
        $this->assertEquals(150, $responseData['price']);
    }

    #[Test]
    public function it_prevents_updating_ad_not_owned_by_user()
    {
        $ownerWorkshop = Workshop::factory()->create(); // الورشة المالكة للإعلان
        $userWorkshop = Workshop::factory()->create(); // ورشة المستخدم الحالي (مختلفة)

        $workshopAd = new WorkshopAd(['id' => 1, 'workshop_id' => $ownerWorkshop->id]);

        $updateData = ['title' => 'New Title'];

        // تأكد من أن دالة update في المستودع لن يتم استدعاؤها
        $this->repositoryMock->shouldNotReceive('update');

        // استدعاء الخدمة بورشة المستخدم المختلفة
        $response = $this->service->updateWorkshopAd($workshopAd, $updateData, $userWorkshop);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode()); // Forbidden
        $this->assertEquals('ليس لديك الصلاحية لتعديل هذا الإعلان', $response->getData(true)['message']);
    }

    #[Test]
    public function it_handles_update_failure_in_repository()
    {
        $workshop = Workshop::factory()->create();
        $workshopAd = new WorkshopAd(['id' => 1, 'workshop_id' => $workshop->id]);
        $updateData = ['title' => 'New Title'];

        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->andReturn(false); // افترض أن التحديث فشل في المستودع

        $response = $this->service->updateWorkshopAd($workshopAd, $updateData, $workshop);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
         $this->assertEquals('حدث خطأ أثناء تعديل الإعلان', $response->getData(true)['message']);
    }

    #[Test]
    public function it_deletes_an_owned_workshop_ad()
    {
        $workshop = Workshop::factory()->create();
        $workshopAd = new WorkshopAd(['id' => 1, 'workshop_id' => $workshop->id]);

        $this->repositoryMock
            ->shouldReceive('delete')
            ->once()
             // تأكد من أنه يستدعي الحذف على الكائن الصحيح
            ->with(Mockery::on(function ($adArg) use ($workshopAd) {
                 return $adArg->id === $workshopAd->id;
             }))
            ->andReturn(true); // افترض أن الحذف نجح

        $response = $this->service->deleteWorkshopAd($workshopAd, $workshop);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode()); // No Content
        $this->assertNull($response->getData()); // لا يوجد محتوى في الاستجابة
    }

    #[Test]
    public function it_prevents_deleting_ad_not_owned_by_user()
    {
        $ownerWorkshop = Workshop::factory()->create();
        $userWorkshop = Workshop::factory()->create();
        $workshopAd = new WorkshopAd(['id' => 1, 'workshop_id' => $ownerWorkshop->id]);

        // تأكد من أن دالة delete في المستودع لن يتم استدعاؤها
        $this->repositoryMock->shouldNotReceive('delete');

        $response = $this->service->deleteWorkshopAd($workshopAd, $userWorkshop);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode()); // Forbidden
        $this->assertEquals('ليس لديك الصلاحية لحذف هذا الإعلان', $response->getData(true)['message']);
    }

    #[Test]
    public function it_handles_delete_failure_in_repository()
    {
        $workshop = Workshop::factory()->create();
        $workshopAd = new WorkshopAd(['id' => 1, 'workshop_id' => $workshop->id]);

        $this->repositoryMock
            ->shouldReceive('delete')
            ->once()
            ->andReturn(false); // افترض أن الحذف فشل

        $response = $this->service->deleteWorkshopAd($workshopAd, $workshop);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('حدث خطأ أثناء حذف الإعلان', $response->getData(true)['message']);
    }
}

