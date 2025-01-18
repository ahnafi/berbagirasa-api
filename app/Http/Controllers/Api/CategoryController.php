<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index() {
        $categories = Category::all();
        return new CategoryResource('success', 'Data retrieved successfully', $categories);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if($validator->fails()) {
            return new CategoryResource('error', $validator->errors(), null);
        }

        $category = Category::create($request->all());
        return new CategoryResource('success', 'Data created successfully', $category);
    }
}
