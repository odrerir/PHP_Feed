<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class FollowController extends Controller
{
    public function __construct(private FollowService $followService) {}

    #[OA\Post(
        path: '/api/users/{username}/follow',
        tags: ['Follow'],
        summary: 'Seguir um usuário',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'username', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Agora está seguindo'),
            new OA\Response(response: 422, description: 'Não é possível seguir a si mesmo'),
        ]
    )]
    public function follow(Request $request, string $username): JsonResponse
    {
        $target = User::where('username', $username)->firstOrFail();
        $this->followService->follow($request->user(), $target);

        return response()->json(['message' => 'Agora você está seguindo '.$target->username.'.']);
    }

    #[OA\Delete(
        path: '/api/users/{username}/follow',
        tags: ['Follow'],
        summary: 'Deixar de seguir um usuário',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'username', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Deixou de seguir'),
        ]
    )]
    public function unfollow(Request $request, string $username): JsonResponse
    {
        $target = User::where('username', $username)->firstOrFail();
        $this->followService->unfollow($request->user(), $target);

        return response()->json(['message' => 'Deixou de seguir '.$target->username.'.']);
    }
}