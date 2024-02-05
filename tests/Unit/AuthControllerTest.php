<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;

use App\Http\Controllers\API\AuthController;
use Tests\TestCase;
use App\Models\User;
use Carbon\Factory as CarbonFactory;
use Database\Factories\UserFactory;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user',
            'authorization' => [
                'token',
                'type',
            ],
        ]);
    }
    public function test_authenticate_user_with_invalid_credentials()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];
        $response = $this->post('/api/login', $data);
        $response->assertStatus(401);
        $response->Json('Verifier vos identifiants');

    }

    public function test_register()
    {
        $response = $this->postJson('/api/register', [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'user' => [
                'id',
                'firstname',
                'lastname',
                'email',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_logout()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
    
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->postJson('/api/refresh');
    
        $response->assertStatus(200);
        $response->Json('Déconnection reuissite');
    }

    public function test_refresh()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->postJson('/api/refresh');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user',
            'authorisation' => [
                'token',
                'type',
            ],
        ]);
    }
}
