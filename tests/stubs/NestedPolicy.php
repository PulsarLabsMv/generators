<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Javaabu\Activitylog\Models\Activity;
use Illuminate\Auth\Access\Response;

class CategoryPostPolicy
{

    public function viewAny(User $user, Category $category): bool
    {
        return $user->can('view posts');
    }

    public function view(User $user, Post $post, Category $category): bool
    {
        return $user->can('view', $category) && $user->can('view posts');
    }

    public function create(User $user, Category $category): bool
    {
        return $user->can('update', $category) && $user->can('edit posts');
    }

    public function update(User $user, Post $post, Category $category): bool
    {
        return $user->can('update', $category) && $user->can('edit posts');
    }

    public function delete(User $user, Post $post, Category $category): bool
    {
        return $user->can('update', $category) && $user->can('delete posts');
    }

    public function viewLogs(User $user, Post $post, Category $category): bool
    {
        return $user->can('viewAny', Activity::class) && $user->can('update', [$post, $category]);
    }
}
