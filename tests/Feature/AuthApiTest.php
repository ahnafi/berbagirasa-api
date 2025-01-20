<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user register
     *
     * @return void
     */
    public function test_user_can_register() : void
    {
        // Structure of the data to be sent
        // 'name' => 'required|string|min:3|max:255',
        // 'email' => 'required|email|unique:users,email',
        // 'phone' => 'required|string|unique:users,phone|min:10|max:15',
        // 'password' => 'required|string|min:8',

        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
            'phone' => '08123456789',
            'password' => 'password',
        ];

        $this->post('/api/register', $data)
            ->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'User registered successfully',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'access_token',
                    'token_type',
                ],
            ]);
    }

    /**
     * Test user login
     *
     * @return void
     */
    public function test_user_can_login(): void
    {
        // Structure of the data to be sent
        // 'email' => 'required|email|unique:users,email',
        // 'password' => 'required|string|min:8',

        $password = 'password';
        $user = User::factory()->create([
            'email' => 'johndoe@gmail.com',
            'password' => bcrypt($password),
        ]);

        $data = [
            'email' => $user->email,
            'password' => $password,
        ];

        $this->post('/api/login', $data)
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'User logged in successfully',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'access_token',
                    'token_type',
                ],
            ]);
    }

    /**
     * Test user profile
     *
     * @return void
     */
    public function test_user_can_fetch_profile(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->get('/api/profile')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'User profile retrieved successfully',
            ]);
    }

    /**
     * Test log out
     *
     * @return void
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/logout')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'User logged out successfully',
            ]);
    }
}
