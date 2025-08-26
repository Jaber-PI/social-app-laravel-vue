<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\GroupUser;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        return true;
    }


    public function create(User $user): bool
    {
        return $user !== null;
    }


    public function update(User $user, Post $post): bool
    {
        if ($user->id === $post->created_by) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        // Owner can always delete their post
        if ($user->id === $post->created_by) {
            return true;
        }

        if ($post->group_id) {
            return $this->isGroupAdmin($user->id, $post->group_id);
        }

        return false;
    }

    public function react(User $user, Post $post): bool
    {
        return true;
    }

    public function comment(User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        // Owner can restore their post
        if ($user->id === $post->created_by) {
            return true;
        }

        // If post belongs to a group, check if user is group admin
        if ($post->group_id) {
            return $this->isGroupMember($user->id, $post->group_id);
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        // Only super admins or system admins should be able to force delete
        return false;
    }

    /**
     * Check if user is an admin of the given group.
     */
    protected function isGroupAdmin(int $userId, int $groupId): bool
    {
        return GroupUser::where('group_id', $groupId)->where('user_id', $userId)
            ->where('role', UserRole::Admin->value)->exists();
    }

    /**
     * Check if user is a member of the given group.
     */
    protected function isGroupMember(int $userId, int $groupId): bool
    {
        return GroupUser::where('group_id', $groupId)->where('user_id', $userId)->exists();
    }

    /**
     * Determine if the user can pin/unpin posts.
     */
    public function pin(User $user, Post $post): bool
    {
        // if ($user->id === $post->created_by) {
        //     return true;
        // }

        // Group admins can pin posts in their groups
        if ($post->group_id) {
            return $this->isGroupAdmin($user->id, $post->group_id);
        }

        return false;
    }
}
