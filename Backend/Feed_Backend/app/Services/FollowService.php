<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class FollowService
{
    public function follow(User $follower, User $target): void
    {
        if ($follower->id === $target->id) {
            throw ValidationException::withMessages(['user' => ['Você não pode seguir a si mesmo.']]);
        }

        if ($follower->following()->where('following_id', $target->id)->exists()) {
            return;
        }

        $follower->following()->attach($target->id);
    }

    public function unfollow(User $follower, User $target): void
    {
        $follower->following()->detach($target->id);
    }
}