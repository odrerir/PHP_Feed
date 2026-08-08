<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(private SearchService $searchService) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->searchService->search($request->query('q'), $request->user())
        );
    }
}