<?php

namespace Tests\Feature\App\Requests\V1;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use App\Requests\V1\FilterVendorRequest;

class FilterVendorRequestTest extends TestCase
{
    public function test_validation_fails_with_invalid_data()
    {
        $data = [
            'filter' => [
                'name' => str_repeat('X', 300),
                'description' => str_repeat('Y', 2000),
                'user_id' => 'not-an-integer',
                'created_at' => 'invalid-date',
                'updated_at' => '01-01-2025',
                'include' => 'user,invalid-field!',
            ],
            'page' => 0,
            'per_page' => 999,
            'sort' => 'name,,email',
        ];

        $validator = Validator::make($data, (new FilterVendorRequest())->rules());

        $this->assertTrue($validator->fails(), 'Invalid data should fail validation.');
    }
}
