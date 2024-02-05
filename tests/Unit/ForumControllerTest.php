<?php

namespace Tests\Unit;

use App\Http\Controllers\ForumController;
use App\Models\User;
use App\Models\Forum;
use Illuminate\Database\Eloquent\Factories\Factory as FactoriesFactory;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\Eloquent\Factories\Factory;


class ForumControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function test_store_method_creates_forum()
    {
        $user = User::factory()->create();
        $fieldId = 1;
        $requestData = [
            'forumname' => 'Test Forum',
            'description' => 'This is a test forum',
            'field_id' => $fieldId
        ];
        
        $user = User::factory()->create();
        
        
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);
        $response = $this->post('/api/addforum', $requestData);
    
        $response->assertStatus(201)
                 ->assertJson(['message' => 'Votre forum a bien été créé.']);
    
        $this->assertDatabaseHas('forums', [
            'forumname' => 'Test Forum',
            'description' => 'This is a test forum',
            'field_id' => $fieldId,
            'user_id' => $user->id
        ]);
    }

    public function test_returns_json_response_with_non_archived_forums()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);
        $response = $this->get('/api/displayforum');
        $response->assertStatus(200);
}
}
