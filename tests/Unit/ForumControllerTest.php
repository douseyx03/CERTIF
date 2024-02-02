<?php

namespace Tests\Unit;

use App\Http\Controllers\ForumController;
use App\Http\Requests\StoreForumRequest;
use App\Models\Forum;
use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\Concerns\InteractsWithLog;
use Tests\TestCase;

class ForumControllerTest extends TestCase
{
    public function test_forum_creation_success(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);
    
        // Arrange
        $request = new StoreForumRequest();
        $request->merge([
            'forumname' => 'Test Forum',
            'description' => 'This is a test forum',
            'field_id' => 1
        ]);
        
        // // Create a test user
        // $user = User::factory()->create();
        
        // // Use actingAs to authenticate the test user
        // $request = $this->actingAs($user);
        // $token = JWTAuth::fromUser($user);
        // $this->withHeader('Authorization', 'Bearer ' . $token);
        $response = $this->post('/api/addfield', $request->all());
        $response -> assertStatus(201)->assertJson(['message' => 'Votre domaine a bien été créé.']);
      
    }

        // Forum creation is logged
// public function test_forum_creation_logging()
// {
//     // Arrange
//     $request = new StoreForumRequest();
//     $request->merge([
//         'forumname' => 'Test Forum',
//         'description' => 'This is a test forum',
//         'field_id' => 1
//     ]);
    
//     // Act
//     $this->post('/apiforums', $request->all());
    
//     // Assert
//     $this->assertLogged('info', 'Forum object created with name: Test Forum');
//     $this->assertLogged('info', 'Forum saved');
//     $this->assertLogged('info', 'Response: Forum created with name: Test Forum');
// }
}
