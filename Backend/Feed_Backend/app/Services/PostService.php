<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class PostService
{
    public function feed(User $viewer, int $perPage = 10)
    {
        return Post::with('user')
            ->withCount(['comments', 'likes'])
            ->latest()
            ->paginate($perPage)
            ->through(fn (Post $post) => $this->formatPost($post, $viewer));
    }

    public function create(User $user, array $data): Post
    {
        return Post::create([
            'user_id'    => $user->id,
            'caption'    => $data['caption'] ?? null,
            'media_path' => $data['media_path'],
        ]);
    }

    public function showWithDetails(Post $post, User $viewer): array
    {
        $post->loadCount(['comments', 'likes'])->load('user');
        return $this->formatPost($post, $viewer);
    }

    public function delete(Post $post, User $user): void
    {
        if ($post->user_id !== $user->id) {
            throw new AuthorizationException('Você só pode excluir os próprios posts.');
        }
        $post->delete();
    }

    private function formatPost(Post $post, User $viewer): array
    {
        return [
            'id'             => $post->id,
            'caption'        => $post->caption,
            'media_path'     => $post->media_path,
            'created_at'     => $post->created_at,
            'user'           => $post->user,
            'comments_count' => $post->comments_count,
            'likes_count'    => $post->likes_count,
            'is_liked'       => $post->isLikedBy($viewer),
        ];
    }
}