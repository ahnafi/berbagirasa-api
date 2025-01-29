<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user register
     *
     * @return void
     */
    public function test_user_can_register(): void
    {
        // Structure of the data to be sent
        // 'name' => 'required|string|min:3|max:255',
        // 'email' => 'required|email|unique:users,email',
        // 'phone' => 'required|string|unique:users,phone|min:10|max:15',
        // 'password' => 'required|string|min:8',

        $this->post('/api/register', [
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
            'phone' => '08123456789',
            'password' => 'Password1@',
        ])
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    'name' => 'John Doe',
                    'email' => 'johndoe@gmail.com',
                    'phone' => '08123456789'
                ]
            ]);
    }

    public function test_user_cannot_register_validation_error(): void
    {
        $this->post('/api/register', [
            'name' => 'John Doe',
            'email' => 'johndoe',
            'phone' => '08123456789111111',
            'password' => 'Passwo',
        ])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "email" => [
                        "The email field must be a valid email address."
                    ],
                    "phone" => [
                        "The phone field must not be greater than 15 characters."
                    ],
                    "password" => [
                        "The password field must be at least 8 characters.",
                        "The password field must contain at least one symbol.",
                        "The password field must contain at least one number."
                    ]
                ]
            ]);
    }

    public function test_user_cannot_register_email_allready_registered(): void
    {
        $this->test_user_can_register();

        $this->post('/api/register', [
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
            'phone' => '08123456789',
            'password' => 'Password1@',
        ])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "email" => [
                        "The email has already been taken."
                    ],
                    "phone" => [
                        "The phone has already been taken."
                    ]
                ]
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
