<?php

namespace Tests\Feature\Requests;

use App\Models\Workshop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Requests\UpdateWorkshopRequest;
use Illuminate\Support\Facades\Validator;


class UpdateWorkshopRequestTest extends TestCase
{
    use RefreshDatabase;

    private Workshop $workshop;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workshop = Workshop::factory()->create();
    }

    public function test_it_passes_with_valid_data()
    {
        $data = [
            'name' => 'Updated Workshop Name',
            'location' => 'Updated Location',
            'description' => 'Updated Description',
            'city' => 'Updated City',
            'commercial_registration_number' => '1234567890', 
            'commercial_registration_image' => 'http://example.com/image.jpg',
            'certification_details' => 'Updated Certification',
        ];

        $request = new UpdateWorkshopRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());

        $this->workshop->update($data);
        $this->assertDatabaseHas('workshops', [
            'id' => $this->workshop->id,
            'name' => 'Updated Workshop Name',
            'location' => 'Updated Location',
        ]);
    }
    
    public function test_it_fails_with_duplicate_commercial_registration_number()
    {
        $existingWorkshop = Workshop::factory()->create();

        $data = [
            'commercial_registration_number' => $existingWorkshop->commercial_registration_number,
        ];

        $request = new UpdateWorkshopRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
    }
}
