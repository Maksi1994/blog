<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function edit(User $user, Article $article) {
        return $article->user_id === $user->id;
    }

    public function delete(User $user, Article $article) {
        return $article->user_id === $user->id;
    }
}
