<?php

namespace App\Services;

use App\Models\User;

class SearchService
{
    public function search(?string $query, User $viewer, int $perPage = 15)
    {
        return User::query()
            ->when($query, fn ($q) => $q->where(function ($sub) use ($query) {
                $sub->where('name', 'like', "%{$query}%")
                    ->orWhere('username', 'like', "%{$query}%");
            }))
            ->where('id', '!=', $viewer->id)
            ->select('id', 'name', 'username', 'avatar_path')
            ->paginate($perPage);
    }
}