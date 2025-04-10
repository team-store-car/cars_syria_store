<?php

namespace Tests\Unit\Requests\Auth;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{


    
    public function test_authorize_returns_true()
    {
        $request = new LoginRequest();
        $this->assertTrue($request->authorize());
    }



    public function test_rules_validation_passes_with_valid_data()
    {
        $request = new LoginRequest();
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->fails());
    }

    public function test_rules_validation_fails_with_missing_email()
    {
        $request = new LoginRequest();
        $data = [
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_rules_validation_fails_with_missing_password()
    {
        $request = new LoginRequest();
        $data = [
            'email' => 'test@example.com',
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_rules_validation_fails_with_invalid_email()
    {
        $request = new LoginRequest();
        $data = [
            'email' => 'invalid-email',
            'password' => 'password123',
        ];

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }
}