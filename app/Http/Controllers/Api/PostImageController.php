<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostImageResource;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostImageController extends Controller
{
    /**
     * index
     *
     * @return PostImageResource
     */
    public function index() : PostImageResource
    {
        $postImages = PostImage::all();

        return new PostImageResource('success', 'Data fetched successfully', $postImages);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return PostImageResource
     */
    public function store(Request $request) : PostImageResource
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'post_id' => 'required|integer|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return new PostImageResource('error', $validator->errors(), null);
        }

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        $postImage = PostImage::create([
            'name' => $imageName,
            'post_id' => $request->post_id,
        ]);

        return new PostImageResource('success', 'Image uploaded successfully', $postImage);
    }
}
