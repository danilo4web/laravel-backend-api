<?php

namespace Tests\Integration;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::find(1);
        $this->user = User::factory()->create();
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

    public function testAdminIsCreatedSucessfully()
    {
        $this->actingAs($this->admin);

        $payload = [
            'name' => 'Danilo',
            'email' => 'danilo@email.com',
            'password' => 'CORRECT_PASSWORD'
        ];

        $this->json('post', '/api/v1/admin/register', $payload)
            ->assertStatus(Response::HTTP_OK);
    }

    public function testAdminPageRegisterNotAllowedForNonAdmin()
    {
        $this->actingAs($this->user);

        $payload = [
            'name' => 'Danilo',
            'email' => 'danilo@email.com',
            'password' => 'CORRECT_PASSWORD'
        ];

        $this->json('post', '/api/v1/admin/register', $payload)
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

    public function testLogout()
    {
        $this->actingAs($this->admin);

        $this->json('post', 'api/v1/logout')
            ->assertStatus(Response::HTTP_OK);
    }

    public function testLoginAsAdmin()
    {
        $payload = [
            'email' => 'admin@bnb.com',
            'password' => '152634789'
        ];

        $this->json('post', 'api/v1/admin/login', $payload)
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'message',
            'access_token',
            'token_type'
        ]);
    }

    public function testNotLoginAsInvalidAdmin()
    {
        $payload = [
            'email' => 'admin@bnb.com',
            'password' => 'WRONG_PASSWORD'
        ];

        $this->json('post', 'api/v1/admin/login', $payload)
        ->assertStatus(Response::HTTP_UNAUTHORIZED);
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
