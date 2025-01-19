<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostImageResource;
use App\Models\PostImage;
use Illuminate\Http\Request;

class PostImageController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index() {
        $postImages = PostImage::all();

        return new PostImageResource('success', 'Data fetched successfully', $postImages);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        $postImage = new PostImage();
        $postImage->image = $imageName;
        $postImage->save();

        return new PostImageResource('success', 'Image uploaded successfully', $postImage);
    }
}
