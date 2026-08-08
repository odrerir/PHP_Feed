<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profileService) {}

    public function show(Request $request): JsonResponse
    {
        return response()->json($this->profileService->showOwn($request->user()));
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
            unset($data['avatar']);
        }

        return response()->json($this->profileService->update($request->user(), $data));
    }

    public function showByUsername(Request $request, string $username): JsonResponse
    {
        $target = User::where('username', $username)->firstOrFail();

        return response()->json($this->profileService->showOther($request->user(), $target));
    }
}