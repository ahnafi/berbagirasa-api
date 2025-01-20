<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test_can_create_post
     *
     * @return void
     */
    public function test_can_create_post()
    {
        $response = $this->postJson('/api/posts', [
            'title' => 'Post Title',
            'description' => 'Post Description',
            'location' => 'Post Location',
            'category_id' => 1,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Post created successfully',
            ]);
    }

    /**
     * test_can_list_posts
     *
     * @return void
     */
    public function test_can_list_posts()
    {
        $posts = Post::factory()->count(5)->create();

        $this->getJson('/api/posts')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Data fetched successfully',
                'data' => $posts->toArray(),
            ]);
    }

    /**
     * test_can_show_post
     *
     * @return void
     */
    public function test_can_show_post()
    {
        $post = Post::factory()->create();

        $this->getJson("/api/posts/{$post->id}")
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Data fetched successfully',
                'data' => $post->toArray(),
            ]);
    }

    /**
     * test_can_update_post
     *
     * @return void
     */
    public function test_can_update_post()
    {
        $post = Post::factory()->create();

        $data = [
            'title' => 'Post Title Updated',
            'description' => 'Post Description Updated',
            'location' => 'Post Location Updated',
            'category_id' => 2,
        ];

        $this->putJson("/api/posts/{$post->id}", $data)
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Post updated successfully',
            ]);
    }

    /**
     * test_can_delete_post
     *
     * @return void
     */
    public function test_can_delete_post()
    {
        $post = Post::factory()->create();

        $this->deleteJson("/api/posts/{$post->id}")
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Post deleted successfully',
            ]);
    }
}
