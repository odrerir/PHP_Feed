<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(private PostService $postService) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->postService->feed($request->user()));
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['media_path'] = $request->file('media')->store('posts', 'public');

        return response()->json($this->postService->create($request->user(), $data), 201);
    }

    public function show(Request $request, Post $post): JsonResponse
    {
        return response()->json($this->postService->showWithDetails($post, $request->user()));
    }

    public function destroy(Request $request, Post $post): JsonResponse
    {
        $this->postService->delete($post, $request->user());
        return response()->json(['message' => 'Post excluído com sucesso.']);
    }
}