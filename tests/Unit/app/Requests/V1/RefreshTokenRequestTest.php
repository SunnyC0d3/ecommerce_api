<?php

namespace Tests\Unit\App\Requests\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Requests\V1\RefreshTokenRequest;
use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

class RefreshTokenRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_validatesAccessTokenAndRefreshTokenField()
    {
        $validData = [
            'access_token' => 'validAccessToken12345',
            'refresh_token' => 'validRefreshToken12345',
        ];

        $request = new RefreshTokenRequest();
        $validator = Validator::make($validData, $request->rules());

        $this->assertFalse($validator->fails());

        $invalidData = [
            'access_token' => '',
            'refresh_token' => '',
        ];

        $validator = Validator::make($invalidData, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('access_token', $validator->errors()->toArray());
        $this->assertArrayHasKey('refresh_token', $validator->errors()->toArray());

        $invalidData = [
            'access_token' => str_repeat('a', 61),
            'refresh_token' => str_repeat('a', 61),
        ];

        $validator = Validator::make($invalidData, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('access_token', $validator->errors()->toArray());
        $this->assertArrayHasKey('refresh_token', $validator->errors()->toArray());
    }

    public function test_allowsValidRefreshTokenToPass()
    {
        $validData = [
            'access_token' => 'validAccessToken12345',
            'refresh_token' => 'validRefreshToken12345',
        ];

        $request = new RefreshTokenRequest();
        $validator = Validator::make($validData, $request->rules());

        $this->assertFalse($validator->fails());
    }
}