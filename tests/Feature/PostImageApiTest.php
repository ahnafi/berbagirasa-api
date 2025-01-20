<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PostImageApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test_can_get_post_images
     *
     * @return void
     */
    public function test_can_get_post_images()
    {
        // $post = Post::factory()->create();

        $this->getJson('/api/post-images')
            ->assertStatus(200);
    }

    /**
     * test_can_upload_post_image
     *
     * @return void
     */
    public function test_can_upload_post_image()
    {
        $post = Post::factory()->create();
        $file = UploadedFile::fake()->image('image.jpg');

        $response = $this->postJson('/api/post-images', [
            'image' => $file,
            'post_id' => $post->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Image uploaded successfully',
            ]);

        $this->assertDatabaseHas('post_images', [
            'post_id' => $post->id,
        ]);
    }

    /**
     * testPostImageIndex
     *
     * @return void
     */
    public function testPostImageIndex()
    {
        $response = $this->getJson('/api/post-images');

        $response->assertStatus(200);
    }

    /**
     * testPostImageStore
     *
     * @return void
     */
    public function testPostImageStore()
    {
        $response = $this->postJson('/api/post-images', [
            'name' => 'image.jpg',
            'post_id' => 1,
        ]);

        $response->assertStatus(200);
    }
}
