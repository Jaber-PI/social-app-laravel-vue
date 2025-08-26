<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(?User $user, Group $group): bool
    {
        if ($group->is_public) {
            return true;
        }

        // Private groups require authentication and approved membership
        if (!$user) {
            return false;
        }

        return $this->isApprovedMember($user, $group);
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, Group $group): bool
    {
        return $this->isAdmin($user, $group) || $this->isOwner($user, $group);
    }

    public function delete(User $user, Group $group): bool
    {
        return $this->isOwner($user, $group);
    }

    public function restore(User $user, Group $group): bool
    {
        return $this->isOwner($user, $group);
    }

    public function forceDelete(User $user, Group $group): bool
    {
        return false;
    }

    public function join(User $user, Group $group): bool
    {
        if ($this->isMember($user, $group) || $this->hasPendingRequest($user, $group)) {
            return false;
        }

        return true;
    }


    public function leave(User $user, Group $group): bool
    {
        if (!$this->isMember($user, $group)) {
            return false;
        }

        return !$this->isOwner($user, $group);
    }

    public function cancelRequest(User $user, Group $group): bool
    {
        return $this->hasPendingRequest($user, $group);
    }

    public function invite(User $user, Group $group): bool
    {
        return $this->isAdmin($user, $group);
    }

    public function approveMembership(User $user, Group $group): bool
    {
        return $this->isAdmin($user, $group);
    }

    public function removeMembers(User $user, Group $group): bool
    {
        return $this->isAdmin($user, $group);
    }

    public function promoteMembers(User $user, Group $group): bool
    {
        return $this->isOwner($user, $group);
    }

    public function demoteMembers(User $user, Group $group): bool
    {
        return $this->isOwner($user, $group);
    }

    public function post(User $user, Group $group): bool
    {
        return $this->isApprovedMember($user, $group);
    }

    public function comment(User $user, Group $group): bool
    {
        return $this->isApprovedMember($user, $group);
    }

    /**
     * Determine whether the user can moderate posts in the group.
     */
    public function moderate(User $user, Group $group): bool
    {
        return $this->isAdmin($user, $group);
    }

    /**
     * Determine whether the user can pin posts in the group.
     */
    public function pinPosts(User $user, Group $group): bool
    {
        return $this->isAdmin($user, $group);
    }

    /**
     * Determine whether the user can manage group settings.
     */
    public function manageSettings(User $user, Group $group): bool
    {
        return $this->isAdmin($user, $group) || $this->isOwner($user, $group);
    }

    /**
     * Determine whether the user can view member list.
     */
    public function viewMembers(?User $user, Group $group): bool
    {
        // Public groups allow anyone to see members
        if ($group->is_public) {
            return true;
        }

        // Private groups require approved membership
        return $user && $this->isApprovedMember($user, $group);
    }



    /**
     * Determine whether the user can view Pending Requests.
     */
    public function viewPendingRequests(?User $user, Group $group): bool
    {
        return $this->isAdmin($user, $group);
    }


    public function viewAdminData(User $user, Group $group): bool
    {
        return $this->isAdmin($user, $group);
    }

    public function transferOwnership(User $user, Group $group): bool
    {
        return $this->isOwner($user, $group);
    }


    // ========== Helper Methods ==========

    /**
     * Check if user is the owner of the group.
     */
    protected function isOwner(User $user, Group $group): bool
    {
        return $user->id === $group->created_by;
    }

    /**
     * Check if user is an admin of the group.
     */
    protected function isAdmin(User $user, Group $group): bool
    {
        return GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->where('role', 'admin')
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Check if user is a member of the group (any status).
     */
    protected function isMember(User $user, Group $group): bool
    {
        return GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->exists();
    }

    /**
     * Check if user is an approved member of the group.
     */
    protected function isApprovedMember(User $user, Group $group): bool
    {
        return GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Check if user has a pending join request.
     */
    protected function hasPendingRequest(User $user, Group $group): bool
    {
        return GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Check if user is admin or owner (convenience method).
     */
    protected function isAdminOrOwner(User $user, Group $group): bool
    {
        return $this->isOwner($user, $group) || $this->isAdmin($user, $group);
    }
}
