<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class SearchController extends Controller
{
    public function __construct(private SearchService $searchService) {}

    #[OA\Get(
        path: '/api/search',
        tags: ['Search'],
        summary: 'Buscar usuários por nome ou username',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de usuários'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->search($request->query('q'), $request->user())
        );
    }
}