<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    function setUp(): void
    {
        parent::setUp();
        User::query()->delete();
    }

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
                    "user" => [
                        'name' => 'John Doe',
                        'email' => 'johndoe@gmail.com',
                        'phone' => '08123456789'
                    ],
                    "token_type" => "Bearer"
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

        $this->seed(UserSeeder::class);

        $this->post('/api/login', [
            "email" => "test@example.com",
            "password" => "@Password1"
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "token_type" => "Bearer"
                ]
            ])
            ->assertJsonStructure([
                "data" => [
                    "access_token",
                    "token_type"
                ]
            ]);
    }

    public function test_user_cannot_login_email_or_password_wrong(): void
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/login', [
            "email" => "testa@example.com",
            "password" => "@Password1a"
        ])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Email or Password is wrong"
                    ]
                ]
            ])
            ->assertJsonStructure([
                "errors" => [
                    "message" => [
                    ]
                ]
            ]);
    }

    public function test_user_cannot_login_email_validation_error(): void
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/login', [
            "email" => "testaaa",
            "password" => "@Pass"
        ])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "email" => [
                        "The email field must be a valid email address."
                    ],
                    "password" => [
                        "The password field must be at least 8 characters.",
                        "The password field must contain at least one number."
                    ]
                ]
            ])
            ->assertJsonStructure([
                "errors" => [
                    "email" => [
                    ]
                ]
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
                "data" => [
                    'message' => [
                        'User logged out successfully',

                    ]
                ]
            ]);
    }
}
