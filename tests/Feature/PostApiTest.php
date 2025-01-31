<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
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
}
