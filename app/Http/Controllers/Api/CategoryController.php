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
     * @return CategoryResource
     */
    public function index() : CategoryResource {
        $categories = Category::all();
        return new CategoryResource('success', 'Data fetched successfully', $categories);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return CategoryResource
     */
    public function store(Request $request) : CategoryResource {
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
     * @return CategoryResource
     */
    public function show($id) : CategoryResource {
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
     * @return CategoryResource
     */
    public function update(Request $request, $id) : CategoryResource
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
     * @return CategoryResource
     */
    public function destroy($id) : CategoryResource{
        $category = Category::find($id);

        if (!$category) {
            return new CategoryResource('error', 'Data not found', null);
        }

        $category->delete();
        return new CategoryResource('success', 'Data deleted successfully', null);
    }
}
