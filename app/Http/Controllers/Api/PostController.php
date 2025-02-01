<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Database\Eloquent\Builder;
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
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): PostCollection
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);

        $posts = Post::query()
            ->when($request->has('title'), function ($query) use ($request) {
                $query->where("title", "like", "%" . $request->input('title') . "%");
            })
            ->when($request->has('userId'), function ($query) use ($request) {
                $query->where('user_id', $request->integer('userId'));
            })
            ->when($request->has('category'), function ($query) use ($request) {
                $query->where("category_id", $request->input('category'));
            })
            ->when($request->has('location'), function ($query) use ($request) {
                $query->where("location", "like", "%" . $request->input('location') . "%");
            })
            ->with(['images', 'user', 'category'])
            ->paginate(perPage: $size, page: $page);

        return new PostCollection($posts);
    }

    /**
     * store
     *
     * @param PostCreateRequest $request
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
     * @param int $id
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
     * @param PostUpdateRequest $request
     * @param int $id
     * @return PostResource
     */
    public function update(PostUpdateRequest $request, int $id): PostResource
    {
        $data = $request->validated();
        $user = Auth::user();
        $post = $user->posts()->find($id);

        if (!$post) throw new HttpResponseException(response([
            "errors" => [
                "message" => [
                    "Post not found"
                ]
            ]
        ], 404));

        $post->update($data);

        return new PostResource($post->load(["images", "category"]));
    }

    /**
     * destroy
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();

        $post = $user->posts()->find($id);

        if (!$post) throw new HttpResponseException(response([
            "errors" => [
                "message" => [
                    "Post not found"
                ]
            ]
        ], 404));

        $post->delete();

        return response()->json([
            "data" => true
        ]);
    }
}
