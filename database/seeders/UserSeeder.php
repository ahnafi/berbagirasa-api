<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->name = "test";
        $user->email = "test@example.com";
        $user->password = "@Password1";
        $user->phone = "081234567";
        $user->save();

        $user2 = new User();
        $user2->name = "test user 2";
        $user2->email = "test2@example.com";
        $user2->password = "@Password1";
        $user2->phone = "0855544332";
        $user2->save();
    }
}
