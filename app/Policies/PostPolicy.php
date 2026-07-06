<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function delete(User $user, Post $post): bool
    {
        return $post->author_id === $user->id;
    }

    public function interact(User $user, Post $post): bool
    {
        return $post->community_id === null || $post->community->isMember($user);
    }
}
