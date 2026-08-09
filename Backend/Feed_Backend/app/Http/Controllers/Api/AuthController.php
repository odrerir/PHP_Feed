<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    #[OA\Post(
        path: '/api/register',
        tags: ['Auth'],
        summary: 'Registrar novo usuário',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name','username','email','password','password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'email', type: 'string'),
                    new OA\Property(property: 'password', type: 'string'),
                    new OA\Property(property: 'password_confirmation', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Usuário registrado com sucesso'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        return response()->json($this->authService->register($request->validated()), 201);
    }

    #[OA\Post(
        path: '/api/login',
        tags: ['Auth'],
        summary: 'Autenticar usuário',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email','password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string'),
                    new OA\Property(property: 'password', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Login realizado com sucesso'),
            new OA\Response(response: 422, description: 'Credenciais inválidas'),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        return response()->json($this->authService->login($request->validated()));
    }

    #[OA\Post(
        path: '/api/logout',
        tags: ['Auth'],
        summary: 'Encerrar sessão (revoga o token atual)',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Logout realizado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());
        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    #[OA\Get(
        path: '/api/me',
        tags: ['Auth'],
        summary: 'Dados do usuário autenticado',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Usuário autenticado'),
        ]
    )]
    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}