<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = new Category();
        $category->id = "FOOD";
        $category->name = "Makanan kering";
        $category->save();

        $category2 = new Category();
        $category2->id = "DRINK";
        $category2->name = "Minuman aja";
        $category2->save();
    }
}
