<?php

namespace App\Services;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;

class LikeService
{
    public function like(User $user, Post $post): void
    {
        Like::firstOrCreate(['user_id' => $user->id, 'post_id' => $post->id]);
    }

    public function unlike(User $user, Post $post): void
    {
        Like::where('user_id', $user->id)->where('post_id', $post->id)->delete();
    }
}