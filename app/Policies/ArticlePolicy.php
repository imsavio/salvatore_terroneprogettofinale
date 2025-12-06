<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Article $article): bool
    {
        // Gli admin possono vedere qualsiasi articolo
        if ($user?->isAdmin()) {
            return true;
        }

        return $article->isPublished() || ($user?->id === $article->user_id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Article $article): bool
    {
        // Gli admin possono modificare qualsiasi articolo
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $article->user_id;
    }

    public function delete(User $user, Article $article): bool
    {
        return $this->update($user, $article);
    }

    public function restore(User $user, Article $article): bool
    {
        return $this->update($user, $article);
    }

    public function forceDelete(User $user, Article $article): bool
    {
        return $this->update($user, $article);
    }
}
