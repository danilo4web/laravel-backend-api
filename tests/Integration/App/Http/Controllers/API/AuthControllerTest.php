<?php

namespace Tests\Integration;

use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testUserIsCreatedSucessfully()
    {
        $payload = [
            'name' => 'Danilo',
            'email' => 'danilo@email.com',
            'password' => 'CORRECT_PASSWORD'
        ];

        $this->json('post', '/api/v1/register', $payload)
            ->assertStatus(Response::HTTP_OK);
    }

    public function testDontRegisterUsersWithEmailDuplication()
    {
        $payload = [
            'name' => 'Danilo',
            'email' => 'danilo@email.com',
            'password' => 'CORRECT_PASSWORD'
        ];

        $this->json('post', '/api/v1/register', $payload)
            ->assertStatus(Response::HTTP_OK);

        $response = $this->json('post', '/api/v1/register', $payload)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'email' => ['The email has already been taken.']
            ]);
    }

    public function testCreateAnUserAndLoginSucessfully()
    {
        $payload = [
            'name' => 'Danilo',
            'email' => 'danilo@danilo.com',
            'password' => 'CORRECT_PASSWORD'
        ];

        $this->json('post', '/api/v1/register', $payload)
            ->assertStatus(Response::HTTP_OK);

        $this->json('post', 'api/v1/login', $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type'
            ]);
    }

    public function testShouldNotLoginWithInvalidData()
    {
        $payload = [
            'email' => 'danilo@danilo.com',
            'password' => 'INVALID_PASSWORD'
        ];
        
        $this->post('api/v1/login', $payload)   
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
