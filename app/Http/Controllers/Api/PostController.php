<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostCreateRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware("auth:sanctum", except: ["index", "show"])
        ];
    }

    /**
     * index
     *
     * @return PostResource
     */
    public function index(): JsonResponse
    {
        $posts = Post::with(['images', "user", "category"])->latest()->paginate(10);

        return response()->json([
            "data" => PostResource::collection($posts)
        ]);
    }

    /**
     * store
     *
     * @param mixed $request
     * @return JsonResponse
     */
    public function store(PostCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        $content = $user->posts()->create($data);

        if ($request->hasfile("images")) {
            foreach ($request->file('images') as $image) {
                $path = $image->storePublicly("contents", "public");
                $content->images()->create(["path" => $path]);
            }
        }

        return (new PostResource($content->load(["images", "category"])))->response()->setStatusCode(201);
    }

    /**
     * show
     *
     * @param mixed $id
     * @return PostResource
     */
    public function show(int $id): PostResource
    {
        $post = Post::with(["user", "images", "category"])->find($id);

        if (!$post) throw new HttpResponseException(response([
            "errors" => [
                "message" => [
                    "Post not found"
                ]
            ]
        ], 404));

        return new PostResource($post);
    }

    /**
     * update
     *
     * @param mixed $request
     * @param mixed $id
     * @return PostResource
     */
    public function update(Request $request, $id): PostResource
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

        $post = Post::find($id);

        if (!$post) {
            return new PostResource('error', 'Post not found', null);
        }

        $post->update([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'category_id' => $request->category_id,
        ]);

        return new PostResource('success', 'Post updated successfully', $post);
    }

    /**
     * destroy
     *
     * @param mixed $id
     * @return PostResource
     */
    public function destroy($id): PostResource
    {
        $post = Post::find($id);

        if (!$post) {
            return new PostResource('error', 'Post not found', null);
        }

        $post->delete();

        return new PostResource('success', 'Post deleted successfully', null);
    }
}
