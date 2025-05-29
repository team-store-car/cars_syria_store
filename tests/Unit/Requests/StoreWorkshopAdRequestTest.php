<?php

namespace Tests\Unit\Requests\Auth;

use Tests\TestCase;
use App\Http\Requests\StoreWorkshopAdRequest;

class StoreWorkshopAdRequestTest extends TestCase
{
    #[Test]
    public function test_it_has_correct_validation_rules()
    {
        $request = new StoreWorkshopAdRequest();

        $this->assertEquals([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
        ], $request->rules());
    }
}
