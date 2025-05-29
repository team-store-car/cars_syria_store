<?php
namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;


class WorkshopControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_user_can_create_workshop()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
    
        $role = Role::firstOrCreate(['name' => 'user']);
        $user = \App\Models\User::factory()->create();
        $user->assignRole($role);
    
        // استخدام الفاكتوري لتوليد بيانات واقعية دون حفظها
        $payload = \App\Models\Workshop::factory()->make([
            'user_id' => null, // لأن الـ API هي التي تعيّن المستخدم المُسجل حالياً
        ])->toArray();
    
        $this->actingAs($user, 'sanctum');
    
        $response = $this->postJson('/api/workshops', $payload);
    
        $response->assertCreated()
                 ->assertJsonFragment([
                     'name' => $payload['name'],
                     'city' => $payload['city'],
                     'commercial_registration_number' => $payload['commercial_registration_number'],
                 ]);
    
        $this->assertDatabaseHas('workshops', [
            'name' => $payload['name'],
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function test_user_can_update_own_workshop()
    {        
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $role = Role::firstOrCreate(['name' => 'workshop', 'guard_name' => 'web']);
    
        $user = User::factory()->create();
        $user->assignRole('workshop');
        $workshop = Workshop::factory()->create(['user_id' => $user->id]);
    
        $payload = ['name' => 'Updated Workshop'];
        $this->actingAs($user, 'sanctum');
        $response = $this->putJson("/api/workshops/{$workshop->id}", $payload);
    
        $response->assertOk()
                 ->assertJsonFragment(['name' => 'Updated Workshop']);
    
        $this->assertDatabaseHas('workshops', [
            'id' => $workshop->id,
            'name' => 'Updated Workshop',
        ]);
    }
    
    #[Test]
    public function test_user_cannot_update_other_users_workshop()
    {        
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $role = Role::firstOrCreate(['name' => 'workshop', 'guard_name' => 'web']);
    
        $owner = User::factory()->create();
        $owner->assignRole('workshop');
        $intruder = User::factory()->create();
        $workshop = Workshop::factory()->create(['user_id' => $owner->id]);
    
        $payload = ['name' => 'Hacked Workshop'];
        $this->actingAs($intruder, 'sanctum');
        $response = $this->putJson("/api/workshops/{$workshop->id}", $payload);
    
        $response->assertForbidden();
    
        $this->assertDatabaseMissing('workshops', ['name' => 'Hacked Workshop']);
    }

    #[Test]
public function test_user_can_delete_own_workshop()
{        
    $this->seed(\Database\Seeders\RoleSeeder::class);
    $role = Role::firstOrCreate(['name' => 'workshop', 'guard_name' => 'web']);

    $user = User::factory()->withRole('workshop')->create();

    $workshop = Workshop::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user, 'sanctum');
    $response = $this->deleteJson("/api/workshops/{$workshop->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('workshops', ['id' => $workshop->id]);
}

#[Test]
public function test_user_cannot_delete_other_users_workshop()
{        
    $this->seed(\Database\Seeders\RoleSeeder::class);
    $role = Role::firstOrCreate(['name' => 'workshop', 'guard_name' => 'web']);

    $owner = User::factory()->withRole('workshop')->create();
    $intruder = User::factory()->create();
    $workshop = Workshop::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($intruder, 'sanctum');
    $response = $this->deleteJson("/api/workshops/{$workshop->id}");

    $response->assertForbidden();

    $this->assertDatabaseHas('workshops', ['id' => $workshop->id]);
}

}
