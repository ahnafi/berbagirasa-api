<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->where("email", "test@example.com")->first();

        Post::query()->create([
            "title" => "test",
            "description" => "test description",
            "location" => "test location",
            "category_id" => "FOOD",
            "user_id" => $user->id
        ]);

        $user2 = User::query()->where("email", "test2@example.com")->first();

        Post::query()->create([
            "title" => "post 2",
            "description" => "test description 2",
            "location" => "test location 2",
            "category_id" => "DRINK",
            "user_id" => $user2->id
        ]);
    }
}
