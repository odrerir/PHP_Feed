<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class LikeController extends Controller
{
    public function __construct(private LikeService $likeService) {}

    #[OA\Post(
        path: '/api/posts/{post}/like',
        tags: ['Likes'],
        summary: 'Curtir um post',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'post', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Curtido'),
        ]
    )]
    public function store(Request $request, Post $post): JsonResponse
    {
        $this->likeService->like($request->user(), $post);
        return response()->json(['message' => 'Curtido.']);
    }

    #[OA\Delete(
        path: '/api/posts/{post}/like',
        tags: ['Likes'],
        summary: 'Remover curtida de um post',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'post', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Curtida removida'),
        ]
    )]
    public function destroy(Request $request, Post $post): JsonResponse
    {
        $this->likeService->unlike($request->user(), $post);
        return response()->json(['message' => 'Curtida removida.']);
    }
}