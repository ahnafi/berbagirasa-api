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
        return new CategoryResource('success', 'Data fetched successfully', $categories);
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

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id) {
        $category = Category::find($id);

        if(!$category) {
            return new CategoryResource('error', 'Data not found', null);
        }

        return new CategoryResource('success', 'Category fetched successfully', $category);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return new CategoryResource('error', 'Data not found', null);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return new CategoryResource('error', $validator->errors(), null);
        }

        $category->update($request->all());
        return new CategoryResource('success', 'Data updated successfully', $category);
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id) {
        $category = Category::find($id);

        if (!$category) {
            return new CategoryResource('error', 'Data not found', null);
        }

        $category->delete();
        return new CategoryResource('success', 'Data deleted successfully', null);
    }
}
