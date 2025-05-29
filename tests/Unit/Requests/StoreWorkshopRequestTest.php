<?php

namespace Tests\Feature\Requests;

use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Http\Requests\StoreWorkshopRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreWorkshopRequestTest extends TestCase
{
    use RefreshDatabase; 

    private function getValidData(): array
    {
        return [
            'name' => 'My Workshop',
            'location' => 'Street 123',
            'description' => 'Some description',
            'city' => 'Riyadh',
            'commercial_registration_number' => '1234567890',
            'commercial_registration_image' => 'https://example.com/image.png',
            'certification_details' => 'Certified by XYZ',
        ];
    }

    #[Test]
    public function test_it_passes_with_valid_data()
    {
        $data = $this->getValidData();

        $request = new StoreWorkshopRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function test_it_fails_when_required_fields_are_missing()
    {
        $data = [];

        $request = new StoreWorkshopRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('location', $validator->errors()->toArray());
        $this->assertArrayHasKey('city', $validator->errors()->toArray());
        $this->assertArrayHasKey('commercial_registration_number', $validator->errors()->toArray());
    }

    #[Test]
    public function test_it_fails_with_invalid_url()
    {
        $data = $this->getValidData();
        $data['commercial_registration_image'] = 'not-a-url';

        $request = new StoreWorkshopRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('commercial_registration_image', $validator->errors()->toArray());
    }
}
