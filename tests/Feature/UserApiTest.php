<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test fetching users
     *
     * @return void
     */
    public function test_fetch_users(): void
    {
        User::factory()->count(5)->create();

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
        // Structure of the data to be sent
        // 'name' => 'required|string|min:3|max:255',
        // 'email' => 'required|email|unique:users,email',
        // 'phone' => 'required|string|unique:users,phone|min:10|max:15',

        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
            'phone' => '08123456789',
            'password' => "johndoegantenk"
        ];

        $this->post('/api/users', $data)
            ->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'User created successfully',
                'data' => [
                    'name' => 'John Doe',
                    'email' => 'johndoe@gmail.com',
                    'phone' => '08123456789',
                ] ,
            ])
            ->assertJsonStructure([
                'status' ,
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    /**
     * Test fetching user by id
     *
     * @return void
     */
    public function test_fetch_user_by_id(): void
    {
        $user = User::factory()->create();

        $this->get("/api/users/{$user->id}")
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'User fetched successfully',
            ]);
    }

    /**
     * Test updating a user
     *
     * @return void
     */
    /* public function test_update_user(): void
    {
        // Structure of the data to be sent
        // 'name' => 'required|string|min:3|max:255',
        // 'email' => 'required|email|unique:users,email,' . $id,
        // 'phone' => 'required|string|unique:users,phone,' . $id . '|min:10|max:15',

        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
            'phone' => '08123456789',
            'password' => 'password',
            'address' => 'Lagos, Nigeria',
            'bio' => 'A software developer',
            'photo' => 'photo.jpg',
        ];

        $this->put('/api/users/1', $data)
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'User updated successfully',
                'data' => $data,
            ]);
    }
    */

    /**
     * Test deleting a user
     *
     * @return void
     */
    public function test_delete_user(): void
    {
        $user = User::factory()->create();

        $this->delete("/api/users/{$user->id}")
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'User deleted successfully',
            ]);
    }
}
