<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test fetching categories
     *
     * @return void
     */
    public function test_fetch_categories(): void
    {
        Category::factory()->create();

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
        $category = Category::factory()->create();

        $this->get("/api/categories/{$category->id}")
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
        $category = Category::factory()->create();
        // Structure of the data to be sent
        // 'name' => 'required|string|max:255',

        $data = [
        'name' => 'Other',
        ];

        $this->put("/api/categories/{$category->id}", $data)
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
        $category = Category::factory()->create();

        $this->delete("/api/categories/{$category->id}")
            ->assertStatus(200)
            ->assertJson([
            'status' => 'success',
            'message' => 'Data deleted successfully',
        ]);
    }
}
