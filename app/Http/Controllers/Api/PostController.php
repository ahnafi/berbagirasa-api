<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * index
     *
     * @return PostResource
     */
    public function index() : PostResource
    {
        $posts = Post::with('images')->get();

        return new PostResource('success', 'Data fetched successfully', $posts);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return PostResource
     */
    public function store(Request $request) : PostResource
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:3|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return new PostResource('error', $validator->errors(), null);
        }

        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
        ]);

        return new PostResource('success', 'Post created successfully', $post);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return PostResource
     */
    public function show($id) : PostResource
    {
        $post = Post::with('images')->find($id);

        if (!$post) {
            return new PostResource('error', 'Post not found', null);
        }

        return new PostResource('success', 'Post fetched successfully', $post);
    }
}
