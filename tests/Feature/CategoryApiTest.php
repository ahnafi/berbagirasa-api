<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryApiTest extends TestCase
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
     * Test fetching categories
     *
     * @return void
     */
    public function test_fetch_categories(): void
    {
        $this->get('/api/categories')
            ->assertStatus(200)
            ->assertJson([
            'status' => 'success',
            'message' => 'Data fetched successfully',
        ]);
    }

    /**
     * Test creating a category
     *
     * @return void
     */
    public function test_create_category(): void
    {
        // Structure of the data to be sent
        // 'name' => 'required|string|max:255',

        $data = [
            'name' => 'Food',
        ];

        $this->post('/api/categories', $data)
            ->assertStatus(201)
            ->assertJson([
            'status' => 'success',
            'message' => 'Data created successfully',
        ]);
    }
}
