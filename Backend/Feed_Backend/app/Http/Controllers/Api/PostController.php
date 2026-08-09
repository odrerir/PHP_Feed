<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PostController extends Controller
{
    public function __construct(private PostService $postService) {}

    #[OA\Get(
        path: '/api/posts',
        tags: ['Posts'],
        summary: 'Listar o feed de posts',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de posts'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        return response()->json($this->postService->feed($request->user()));
    }

    #[OA\Post(
        path: '/api/posts',
        tags: ['Posts'],
        summary: 'Criar um novo post',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['media'],
                    properties: [
                        new OA\Property(property: 'caption', type: 'string'),
                        new OA\Property(property: 'media', type: 'string', format: 'binary'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Post criado com sucesso'),
        ]
    )]
    public function store(StorePostRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['media_path'] = $request->file('media')->store('posts', 'public');

        return response()->json($this->postService->create($request->user(), $data), 201);
    }

    #[OA\Get(
        path: '/api/posts/{post}',
        tags: ['Posts'],
        summary: 'Ver um post individual',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'post', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Dados completos do post'),
            new OA\Response(response: 404, description: 'Post não encontrado'),
        ]
    )]
    public function show(Request $request, Post $post): JsonResponse
    {
        return response()->json($this->postService->showWithDetails($post, $request->user()));
    }

    #[OA\Delete(
        path: '/api/posts/{post}',
        tags: ['Posts'],
        summary: 'Excluir um post (somente o autor)',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'post', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Post excluído com sucesso'),
            new OA\Response(response: 403, description: 'Você só pode excluir os próprios posts'),
        ]
    )]
    public function destroy(Request $request, Post $post): JsonResponse
    {
        $this->postService->delete($post, $request->user());
        return response()->json(['message' => 'Post excluído com sucesso.']);
    }
}