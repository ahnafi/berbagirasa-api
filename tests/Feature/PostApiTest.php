<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\PostSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostApiTest extends TestCase
{

    function setUp(): void
    {
        parent::setUp();
        Storage::fake("public");
        PostImage::query()->forceDelete();
        Post::query()->forceDelete();
        Category::query()->delete();
        User::query()->delete();
        $this->seed([CategorySeeder::class, UserSeeder::class]);
    }

    function test_create_post_success()
    {
        $user = User::where("email", "test@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;
        Storage::fake("public");
        $img = UploadedFile::fake()->image("foto kapan ya.jpg")->size("1000");
        $img2 = UploadedFile::fake()->image("foto kapan ya.jpg")->size("1000");
        $img3 = UploadedFile::fake()->image("foto kapan ya.jpg")->size("1000");

        $this->post("/api/posts", [
            "title" => "test",
            "description" => "test description",
            "location" => "test location",
            "category_id" => "FOOD",
            "images" => [
                $img, $img2, $img3
            ]
        ],
            [
                "Authorization" => "Bearer $token"
            ])
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "title" => "test",
                    "description" => "test description",
                    "location" => "test location",
                ]
            ]);

    }

    function test_create_post_failed_category_not_exist()
    {
        $user = User::where("email", "test@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;
        Storage::fake("public");
        $img = UploadedFile::fake()->image("foto kapan ya.jpg")->size("1000");
        $img2 = UploadedFile::fake()->image("foto kapan ya.jpg")->size("1000");
        $img3 = UploadedFile::fake()->image("foto kapan ya.jpg")->size("1000");

        $this->post("/api/posts", [
            "title" => "test",
            "description" => "test description",
            "location" => "test location",
            "category_id" => "asolole",
            "images" => [
                $img, $img2, $img3
            ]
        ],
            [
                "Authorization" => "Bearer $token"
            ])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "category_id" => [
                        "The selected category id is invalid."
                    ]
                ]
            ]);

    }

    function test_create_post_unauthorized()
    {
        $this->post("/api/posts", [
            "title" => "test",
            "description" => "test description",
            "location" => "test location",
        ], [])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Unauthorized"
                    ]
                ]
            ]);
    }

    function test_create_post_image_not_valid()
    {
        $user = User::where("email", "test@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;
        Storage::fake("public");
        $img = UploadedFile::fake()->image("foto kapan ya.jpg")->size("3000");
        $img2 = UploadedFile::fake()->image("foto kapan ya");

        $this->post("/api/posts", [
            "title" => "test",
            "description" => "test description",
            "location" => "test location",
            "images" => [
                $img, $img2
            ]
        ], [
            "Authorization" => "Bearer $token"
        ])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "images.0" => [
                        "The images.0 field must not be greater than 2000 kilobytes."
                    ],
                    "images.1" => [
                        "The images.1 field must be an image."
                    ]
                ]
            ]);
    }

    function test_create_post_field_required()
    {
        $user = User::where("email", "test@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->post("/api/posts", [

        ], [
            "Authorization" => "Bearer $token"
        ])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "title" => [
                        "The title field is required."
                    ],
                    "description" => [
                        "The description field is required."
                    ],
                    "location" => [
                        "The location field is required."
                    ]
                ]
            ]);
    }

    function test_get_post_by_id_success()
    {
        $this->seed(PostSeeder::class);
        $post = Post::where("title", "test")->first();

        $this->get("/api/posts/$post->id")
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "title" => "test",
                    "description" => "test description",
                    "location" => "test location",
                    "category" => [
                        "id" => "FOOD",
                        "name" => "Makanan kering"
                    ],
                    "images" => [],
                    "author" => [
                        "name" => "test",
                        "email" => "test@example.com",
                        "phone" => "081234567",
                        "address" => null,
                        "bio" => null,
                        "photo" => null,
                    ]
                ]
            ]);
    }

    function test_get_post_by_id_not_found()
    {
        $this->seed(PostSeeder::class);
        $post = Post::where("title", "test")->first();

        $this->get("/api/posts/" . $post->id + 100)
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Post not found",
                    ]
                ]
            ]);
    }

    function test_update_post_success()
    {
        $this->seed(PostSeeder::class);
        $user = User::where("email", "test@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        $post = Post::where("title", "test")->first();

        $this->patch("/api/posts/" . $post->id, [
            "title" => "ini update",
            "description" => "desc update",
            "location" => "location update",
            "category_id" => "DRINK"
        ], ["Authorization" => "Bearer $token"])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "title" => "ini update",
                    "description" => "desc update",
                    "location" => "location update",
                    "category" => [
                        "id" => "DRINK"
                    ],
                    "images" => [],
                    "author" => [
                        "id" => $user->id,
                    ]
                ]
            ]);
    }

    function test_update_post_failed_post_not_found()
    {
        $this->seed(PostSeeder::class);
        $user = User::where("email", "test@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        $post = Post::where("title", "test")->first();

        $this->patch("/api/posts/" . $post->id + 1, [
            "title" => "ini update",
            "description" => "desc update",
            "location" => "location update",
            "category_id" => "DRINK"
        ], ["Authorization" => "Bearer $token"])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Post not found"
                    ]
                ]
            ]);
    }

    function test_update_post_failed_user_must_cannot_edit()
    {
        $this->seed(PostSeeder::class);
        $user = User::where("email", "test2@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        $post = Post::where("title", "test")->first();

        $this->patch("/api/posts/" . $post->id, [
            "title" => "ini update",
            "description" => "desc update",
            "location" => "location update",
            "category_id" => "DRINK"
        ], ["Authorization" => "Bearer $token"])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Post not found"
                    ]
                ]
            ]);
    }

    function test_update_post_failed_unauthorized()
    {
        $this->seed(PostSeeder::class);
        $user = User::where("email", "test2@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        $post = Post::where("title", "test")->first();

        $this->patch("/api/posts/" . $post->id, [
            "title" => "ini update",
            "description" => "desc update",
            "location" => "location update",
            "category_id" => "DRINK"
        ], [])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Unauthorized"
                    ]
                ]
            ]);
    }

    public function test_delete_post_success()
    {
        $this->seed(PostSeeder::class);
        $user = User::where("email", "test@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        $post = $user->posts()->first();

        $this->delete("/api/posts/" . $post->id, [], ["Authorization" => "Bearer $token"])
            ->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);

        $deleted = Post::onlyTrashed()->find($post->id);
        Log::info(json_encode($deleted, JSON_PRETTY_PRINT));
        self::assertNotNull($deleted);
    }

    public function test_delete_post_unauthorized()
    {
        $this->seed(PostSeeder::class);
        $user = User::where("email", "test@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        $post = $user->posts()->first();

        $this->delete("/api/posts/" . $post->id, [], [])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Unauthorized"
                    ]
                ]
            ]);
    }

    public function test_delete_post_not_found()
    {
        $this->seed(PostSeeder::class);
        $user = User::where("email", "test@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        $post = $user->posts()->first();

        $this->delete("/api/posts/" . $post->id + 100, [], ["Authorization" => "Bearer $token"])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Post not found"
                    ]
                ]
            ]);

        $post = Post::where("title", "post 2")->first();
        self::assertNotNull($post);

        $this->delete("/api/posts/" . $post->id, [], ["Authorization" => "Bearer $token"])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Post not found"
                    ]
                ]
            ]);
    }
}
