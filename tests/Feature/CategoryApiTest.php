<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
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

    /**
     * Test fetching category by id
     *
     * @return void
     */
    public function test_fetch_category_by_id(): void
    {
        $this->get('/api/categories/1')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Category fetched successfully',
            ]);
    }

    /**
     * Test updating a category
     *
     * @return void
     */
    public function test_update_category(): void
    {
        // Structure of the data to be sent
        // 'name' => 'required|string|max:255',

        $data = [
        'name' => 'Other',
        ];

        $this->put('/api/categories/1', $data)
            ->assertStatus(200)
            ->assertJson([
            'status' => 'success',
            'message' => 'Data updated successfully',
        ]);
    }

    /**
     * Test deleting a category
     *
     * @return void
     */
    public function test_delete_category(): void
    {
        $this->delete('/api/categories/1')
            ->assertStatus(200)
            ->assertJson([
            'status' => 'success',
            'message' => 'Data deleted successfully',
        ]);
    }
}
