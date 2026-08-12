<?php

namespace App\Services;

use App\Models\User;

class SearchService
{
    public function search(?string $query, User $viewer, int $perPage = 15)
    {
        return User::query()
            ->when($query, function ($q) use ($query) {
                // match from start of field (prefix match)
                $like = "{$query}%";
                $q->where(function ($q) use ($like) {
                    $q->where('username', 'like', $like)
                      ->orWhere('name', 'like', $like);
                });
            })
            ->orderBy('name')
            ->where('id', '!=', $viewer->id)
            ->select('id', 'name', 'username', 'avatar_path')
            ->paginate($perPage);
    }
}