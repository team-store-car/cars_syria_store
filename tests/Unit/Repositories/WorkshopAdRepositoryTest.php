<?php

// ... (Imports and existing code)
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User; // تأكد من وجود هذا
use App\Models\Workshop;
use App\Models\WorkshopAd;
use App\Repositories\WorkshopAdRepository;
use PHPUnit\Framework\Attributes\Test;

class WorkshopAdRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private WorkshopAdRepository $repository;
    private Workshop $workshop;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new WorkshopAdRepository();
        // إنشاء ورشة افتراضية لكل اختبار في هذا الكلاس
        $this->workshop = Workshop::factory()->create();
    }

    #[Test]
    public function it_can_create_a_workshop_ad()
    {
        $data = [
            'workshop_id' => $this->workshop->id,
            'title'       => 'Test Ad Create',
            'description' => 'Test Description Create',
            'price'       => 100,
        ];

        $workshopAd = $this->repository->create($data);

        $this->assertInstanceOf(WorkshopAd::class, $workshopAd);
        $this->assertDatabaseHas('workshop_ads', [
            'id'          => $workshopAd->id,
            'workshop_id' => $this->workshop->id,
            'title'       => 'Test Ad Create',
        ]);
    }

    #[Test]
    public function it_can_find_a_workshop_ad_by_id()
    {
        $createdAd = WorkshopAd::factory()->create(['workshop_id' => $this->workshop->id]);

        $foundAd = $this->repository->find($createdAd->id);

        $this->assertInstanceOf(WorkshopAd::class, $foundAd);
        $this->assertEquals($createdAd->id, $foundAd->id);
    }

    #[Test]
    public function it_returns_null_when_finding_a_non_existent_workshop_ad()
    {
        $foundAd = $this->repository->find(999); // ID غير موجود

        $this->assertNull($foundAd);
    }

    #[Test]
    public function it_can_update_a_workshop_ad()
    {
        $workshopAd = WorkshopAd::factory()->create([
            'workshop_id' => $this->workshop->id,
            'title' => 'Old Title'
        ]);

        $updateData = ['title' => 'New Title', 'price' => 150];
        $result = $this->repository->update($workshopAd, $updateData);

        $this->assertTrue($result); // تأكد من أن الدالة update ترجع true
        $this->assertDatabaseHas('workshop_ads', [
            'id'    => $workshopAd->id,
            'title' => 'New Title',
            'price' => 150,
        ]);
         // تأكد من أن البيانات في الكائن الممرر تم تحديثها أيضًا (إذا كانت update() تعدله مباشرة)
        $this->assertEquals('New Title', $workshopAd->fresh()->title);
    }

    #[Test]
    public function it_can_delete_a_workshop_ad()
    {
        $workshopAd = WorkshopAd::factory()->create(['workshop_id' => $this->workshop->id]);
        $adId = $workshopAd->id; // احفظ الـ ID قبل الحذف

        $result = $this->repository->delete($workshopAd);

        $this->assertTrue($result); // تأكد من أن الدالة delete ترجع true
        $this->assertDatabaseMissing('workshop_ads', ['id' => $adId]);
        // أو يمكنك محاولة البحث عنه وتوقع null
        // $this->assertNull($this->repository->find($adId));
    }
}
