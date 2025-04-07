<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Workshop;
// تأكد من إزالة App\Models\WorkshopAd إذا لم تكن مستخدمة مباشرة هنا
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route; // أضف هذا إذا لم يكن موجوداً

class WorkshopAdControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_allows_authenticated_workshop_owners_to_create_ads()
    {
        $user = User::factory()->create(['role' => 'workshop']);
        $workshop = Workshop::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->postJson('\api\workshopads', [ 
            'title'       => 'Test Ad',
            'description' => 'Test Description',
            'price'       => 100,
        ]);

        $response->assertCreated()
                 ->assertJsonStructure(['id', 'title', 'description', 'price']); // تأكد أن هذا ما يتم إرجاعه فعلاً

        $this->assertDatabaseHas('workshop_ads', ['title' => 'Test Ad']);
    }

    /** @test */
    public function it_prevents_users_without_workshops_from_creating_ads()
    {
        $user = User::factory()->create(); 

     
        $this->actingAs($user)
             ->postJson('\api\workshopads', [ 
                 'title'       => 'Test Ad',
                 'description' => 'Test Description',
                 'price'       => 100,
             ])
             ->assertForbidden() 
             ->assertJson(['message' => 'يجب أن تكون مالك ورشة لإنشاء إعلان']);
    }

    /** @test */
    public function it_validates_workshop_ad_data()
    {
        $user = User::factory()->create(['role' => 'workshop']); 
        $workshop = Workshop::factory()->create(['user_id' => $user->id]);

     
        $this->actingAs($user)
             ->postJson('\api\workshopads', []) 
             ->assertStatus(422)
             ->assertJsonValidationErrors(['title', 'description', 'price']);
    }
}