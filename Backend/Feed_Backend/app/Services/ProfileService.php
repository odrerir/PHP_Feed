<?php

namespace App\Services;

use App\Models\User;

class ProfileService
{
    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }

    public function showOwn(User $user): array
    {
        return [
            'user' => $user,
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
            'posts_count' => $user->posts()->count(),
        ];
    }

    public function showOther(User $viewer, User $target): array
    {
        return [
            'user' => $target,
            'followers_count' => $target->followers()->count(),
            'following_count' => $target->following()->count(),
            'is_following' => $viewer->following()->where('following_id', $target->id)->exists(),
            'is_self' => $viewer->id === $target->id,
            'posts_count' => $target->posts()->count(),
        ];
    }
}