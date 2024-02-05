<?php

namespace Tests\Unit;

use App\Http\Controllers\FieldController;
use App\Http\Requests\StoreFieldRequest;
use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Mockery;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class FieldControllerTest extends TestCase
{

    public function test_returns_json_response_with_non_archived_fields()
    {
        $response = $this->get('/api/displayfield');

        $response->assertStatus(200)
            ->assertJson(Field::where('is_archived', false)->get()->toArray());
    }

    // Returns an empty JSON response if there are no non-archived fields
    public function test_returns_empty_json_response_if_no_non_archived_fields()
    {
        Field::where('is_archived', false)->delete();
    
        $response = $this->get('/api/displayfield');
    
        $response->assertStatus(200)
            ->assertExactJson([]);
    }

    

    public function testStoreMethod()
    {
       
        // Set up the request data
        $picture = UploadedFile::fake()->image('test_picture.jpg');
        $requestData = [
            'fieldname' => 'Test Field',
            'description' => 'Test Description',
            'picture' => $picture,
        ];
        
        
        // Create a test user
        $user = User::factory()->create();
        
        // Use actingAs to authenticate the test user
        // $response = $this->actingAs($user);
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);
        $response = $this->post('/api/addfield', $requestData);
        $response -> assertStatus(201)->assertJson(['message' => 'Votre domaine a bien été créé.']);
       
    }

        // Sets the 'is_archived' attribute of the given Field object to true.
    public function test_sets_is_archived_attribute_to_true()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);

        // Arrange
        $field = new Field();
        
        // Act
        $fieldController = new FieldController();
        $fieldController->destroy($field);
        
        // Assert
        $this->assertTrue($field->is_archived);
    }

}
   