<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profileService) {}

    #[OA\Get(
        path: '/api/profile',
        tags: ['Profile'],
        summary: 'Ver o próprio perfil',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Dados do perfil próprio'),
        ]
    )]
    public function show(Request $request): JsonResponse
    {
        return response()->json($this->profileService->showOwn($request->user()));
    }

    #[OA\Put(
        path: '/api/profile',
        tags: ['Profile'],
        summary: 'Editar o próprio perfil',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'username', type: 'string'),
                        new OA\Property(property: 'bio', type: 'string'),
                        new OA\Property(property: 'avatar', type: 'string', format: 'binary'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Perfil atualizado'),
            new OA\Response(response: 422, description: 'Erro de validação (ex: username já existe)'),
        ]
    )]
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
            unset($data['avatar']);
        }

        return response()->json($this->profileService->update($request->user(), $data));
    }

    #[OA\Get(
        path: '/api/users/{username}',
        tags: ['Profile'],
        summary: 'Ver perfil de outro usuário',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'username', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Dados do perfil'),
            new OA\Response(response: 404, description: 'Usuário não encontrado'),
        ]
    )]
    public function showByUsername(Request $request, string $username): JsonResponse
    {
        $target = User::where('username', $username)->firstOrFail();

        return response()->json($this->profileService->showOther($request->user(), $target));
    }
}