<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PostController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PostResource::collection(Post::query()->latest()->paginate(10));
    }

    public function store(Request $request): JsonResponse
    {
        $post = Post::create($request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]));

        return (new PostResource($post))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Post $post): PostResource
    {
        return new PostResource($post);
    }

    public function update(Request $request, Post $post): PostResource
    {
        $post->update($request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'body' => ['sometimes', 'required', 'string'],
        ]));

        return new PostResource($post->refresh());
    }

    public function destroy(Post $post): Response
    {
        $post->delete();

        return response()->noContent();
    }
}
