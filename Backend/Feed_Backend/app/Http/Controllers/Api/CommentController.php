<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct(private CommentService $commentService) {}

    public function index(Post $post): JsonResponse
    {
        return response()->json($this->commentService->list($post));
    }

    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        $comment = $this->commentService->create($request->user(), $post, $request->validated());
        return response()->json($comment->load('user'), 201);
    }
}