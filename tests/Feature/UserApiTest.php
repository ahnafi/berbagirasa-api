<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        User::query()->delete();
    }

    function test_get_user_current()
    {
        $this->seed(UserSeeder::class);

        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->get("/api/users/current", [
            "Authorization" => "Bearer $token"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "phone" => $user->phone,
                    "photo" => $user->photo,
                    "address" => $user->address,
                    "bio" => $user->bio,
                ]
            ]);
    }

    function test_get_user_current_failed_unauthorized()
    {

        $this->get("/api/users/current")
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Unauthorized"
                    ]
                ]
            ]);
    }

    function test_update_user_success()
    {
        $this->seed(UserSeeder::class);
        $user = User::where("email", "test@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        Storage::fake("public");
        $file = UploadedFile::fake()->image("asolole.jpg")->size(1024);

        $this->patch("/api/users/current",
            [
                "name" => "budiono siregar",
                "bio" => "intinya gitu",
                "address" => "jalan ini itu adalah",
                "photo" => $file,
                "password" => "budiPasw@#123",
                "phone" => "0823345346"
            ],
            [
                "Authorization" => "Bearer $token"
            ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => $user->id,
                    "name" => "budiono siregar",
                    "bio" => "intinya gitu",
                    "address" => "jalan ini itu adalah",
                    "phone" => "0823345346"
                ]
            ]);
    }

    function test_update_user_failed_unauthorized()
    {
        $this->seed(UserSeeder::class);
        $user = User::where("email", "test@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        Storage::fake("public");
        $file = UploadedFile::fake()->image("asolole.jpg")->size(1024);

        $this->patch("/api/users/current",
            [
                "name" => "budiono siregar",
                "bio" => "intinya gitu",
                "address" => "jalan ini itu adalah",
                "photo" => $file,
                "password" => "budiPasw@#123",
                "phone" => "0823345346"
            ],
        )
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Unauthorized"
                    ]
                ]
            ]);
    }

    function test_update_user_failed_no_field()
    {
        $this->seed(UserSeeder::class);
        $user = User::where("email", "test@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        Storage::fake("public");
        $file = UploadedFile::fake()->image("asolole.jpg")->size(1024);

        $this->patch("/api/users/current",
            [
                //
            ],
            [
                "Authorization" => "Bearer $token"
            ]
        )
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "At least one field must be present."
                    ]
                ]
            ]);
    }

    function test_update_user_failed_phone_is_already_taken()
    {
        $this->seed(UserSeeder::class);
        $user = User::where("email", "test@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        $anotherUser = User::factory()->create();

        Storage::fake("public");
        $file = UploadedFile::fake()->image("asolole.jpg")->size(1024);

        $this->patch("/api/users/current",
            [
                "phone" => $anotherUser->phone
            ],
            [
                "Authorization" => "Bearer $token"
            ]
        )
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "phone" => [
                        "The phone has already been taken."
                    ]
                ]
            ]);
    }

    function test_update_user_failed_password_is_weak()
    {
        $this->seed(UserSeeder::class);
        $user = User::where("email", "test@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        Storage::fake("public");
        $file = UploadedFile::fake()->image("asolole.jpg")->size(1024);

        $this->patch("/api/users/current",
            [
                "password" => "budionosiregar",
            ],
            [
                "Authorization" => "Bearer $token"
            ]
        )
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "password" => [
                        "The password field must contain at least one uppercase and one lowercase letter.",
                        "The password field must contain at least one symbol.",
                        "The password field must contain at least one number."
                    ]
                ]
            ]);
    }

    function test_update_user_failed_image_size_max2mb()
    {
        $this->seed(UserSeeder::class);
        $user = User::where("email", "test@example.com")->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        Storage::fake("public");
        $file = UploadedFile::fake()->image("asolole.jpg")->size(2049);

        $this->patch("/api/users/current",
            [
                "photo" => $file,
            ],
            [
                "Authorization" => "Bearer $token"
            ]
        )
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "photo" => [
                        "The photo field must not be greater than 2000 kilobytes."
                    ]
                ]
            ]);
    }

}
