<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CommentController extends Controller
{
    public function __construct(private CommentService $commentService) {}

    #[OA\Get(
        path: '/api/posts/{post}/comments',
        tags: ['Comments'],
        summary: 'Listar comentários de um post',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'post', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de comentários'),
        ]
    )]
    public function index(Post $post): JsonResponse
    {
        return response()->json($this->commentService->list($post));
    }

    #[OA\Post(
        path: '/api/posts/{post}/comments',
        tags: ['Comments'],
        summary: 'Comentar em um post',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'post', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['content'],
                properties: [
                    new OA\Property(property: 'content', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Comentário criado'),
        ]
    )]
    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        $comment = $this->commentService->create($request->user(), $post, $request->validated());
        return response()->json($comment->load('user'), 201);
    }
}