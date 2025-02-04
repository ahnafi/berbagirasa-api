<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function all(Request $request): JsonResponse
    {
        return response()->json([
            "data" => [
                Category::all()
            ]
        ]);
    }
}
