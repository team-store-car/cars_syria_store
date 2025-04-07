<?php

use App\Models\Workshop;
use App\Models\WorkshopAd;
use App\Repositories\WorkshopAdRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopAdRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_workshop_ad()
    {
     
        $workshop = Workshop::factory()->create();

    
        $repository = new WorkshopAdRepository();

  
        $workshopAd = $repository->create([
            'workshop_id' => $workshop->id,
            'title'       => 'Test Ad',
            'description' => 'Test Description',
            'price'       => 100,
        ]);

     
        $this->assertDatabaseHas('workshop_ads', [
            'id'          => $workshopAd->id,
            'workshop_id' => $workshop->id, 
            'title'       => 'Test Ad',
        ]);
    }
}
