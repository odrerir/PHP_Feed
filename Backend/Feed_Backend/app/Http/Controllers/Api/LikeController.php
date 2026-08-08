<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function __construct(private LikeService $likeService) {}

    public function store(Request $request, Post $post): JsonResponse
    {
        $this->likeService->like($request->user(), $post);
        return response()->json(['message' => 'Curtido.']);
    }

    public function destroy(Request $request, Post $post): JsonResponse
    {
        $this->likeService->unlike($request->user(), $post);
        return response()->json(['message' => 'Curtida removida.']);
    }
}