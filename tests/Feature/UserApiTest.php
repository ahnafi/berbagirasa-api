<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test fetching users
     *
     * @return void
     */
    public function test_fetch_users(): void
    {
        $this->get('/api/users')
            ->assertStatus(200)
            ->assertJson([
            'status' => 'success',
            'message' => 'Data fetched successfully',
        ]);
    }

    /**
     * Test creating a user
     *
     * @return void
     */
    public function test_create_user(): void
    {
        \DB::table('users')->truncate();
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

        $this->post('/api/users', $data)
            ->assertStatus(201)
            ->assertJson([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => $data,
        ]);
    }
}
