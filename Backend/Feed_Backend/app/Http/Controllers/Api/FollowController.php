<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function __construct(private FollowService $followService) {}

    public function follow(Request $request, string $username): JsonResponse
    {
        $target = User::where('username', $username)->firstOrFail();
        $this->followService->follow($request->user(), $target);

        return response()->json(['message' => 'Agora você está seguindo '.$target->username.'.']);
    }

    public function unfollow(Request $request, string $username): JsonResponse
    {
        $target = User::where('username', $username)->firstOrFail();
        $this->followService->unfollow($request->user(), $target);

        return response()->json(['message' => 'Deixou de seguir '.$target->username.'.']);
    }
}