<?php

namespace Tests\Unit\Modell;

use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopAd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class WorkshopTest extends TestCase
{
    use RefreshDatabase;


    public function test_it_belongs_to_a_user()
    {
        $workshop = Workshop::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $workshop->user());
        $this->assertInstanceOf(User::class, $workshop->user()->getModel());
    }


    public function test_it_has_many_workshop_ads()
    {
        $workshop = Workshop::factory()->create();
        $ad = WorkshopAd::factory()->create(['workshop_id' => $workshop->id]);

        $this->assertInstanceOf(HasMany::class, $workshop->workshopAds());
        $this->assertTrue($workshop->workshopAds->contains($ad));
    }
}
