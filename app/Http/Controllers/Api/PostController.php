<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $orderColumn = request('order_column', 'created_at');
        if (! in_array($orderColumn, ['id', 'title', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        $orderDirection = request('order_direction', 'desc');
        if (! in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        $posts = Post::with('category')
        ->when(request('category'), function ($query) {
            return $query->where('category_id', request('category'));
        })
        ->when(request('search_category'), function (Builder $query) {
            $query->where('category_id', request('search_category'));
        })
        ->when(request('search_id'), function (Builder $query) {
            $query->where('id', request('search_id'));
        })
        ->when(request('search_title'), function (Builder $query) {
            $query->where('title', 'like', '%' . request('search_title') . '%');
        })
        ->when(request('search_content'), function (Builder $query) {
            $query->where('content', 'like', '%' . request('search_content') . '%');
        })
        ->orderBy($orderColumn, $orderDirection)
        ->paginate(10);

        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request)
    {
        $post = Post::create($request->validated());

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = $file->getClientOriginalName();

            // Store the file in the 'posts' directory within the default disk
            $path = $file->storeAs('posts', $filename);

            info($path);
        }

        return new PostResource($post);
    }

    public function show(Post $post)
    {
        return new PostResource($post);
    }

    public function update(Post $post, StorePostRequest $request)
    {
        $post->update($request->validated());

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = $file->getClientOriginalName();

            // Store the file in the 'posts' directory within the default disk
            $path = $file->storeAs('posts', $filename);

            info($path);
        }

        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response()->noContent();
    }
}
