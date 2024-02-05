<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\CreatesApplication;
use App\Http\Requests\StoreFieldRequest;
use Illuminate\Http\UploadedFile;
// use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Validator;
use Tests\CreatesApplication as TestsCreatesApplication;

class StoreFieldRequestTest extends TestCase
{
    
    use TestsCreatesApplication;
    public function test_valid_fieldname_description_and_picture()
    {
        $validator = Validator::make([
            'fieldname' => 'Valid Fieldname',
            'description' => 'Valid Description',
            'picture' => UploadedFile::fake()->image('picture.jpg')->size(2048),
        ], (new \App\Http\Requests\StoreFieldRequest())->rules());

        $this->assertTrue($validator->passes());
    }

}

