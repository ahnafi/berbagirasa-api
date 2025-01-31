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

        $data = [
            "title" => "test",
            "description" => "test description",
            "location" => "test location",
            "category_id" => "FOOD",
            "user_id" => $user->id
        ];

        Post::query()->create($data);
    }
}
