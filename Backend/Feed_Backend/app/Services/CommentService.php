<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;

class CommentService
{
    public function list(Post $post, int $perPage = 15)
    {
        return $post->comments()->with('user')->latest()->paginate($perPage);
    }

    public function create(User $user, Post $post, array $data): Comment
    {
        return $post->comments()->create([
            'user_id' => $user->id,
            'content' => $data['content'],
        ]);
    }
}