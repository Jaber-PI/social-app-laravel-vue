<?php

namespace App\Http\Resources;

use App\Enums\MembershipStatus;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GroupResource extends JsonResource
{

    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'is_public' => $this->is_public,

            'creator' => new UserResource($this->whenLoaded('creator')),

            'cover_path' => Storage::url($this->cover_path),
            'avatar_path' => Storage::url($this->thumbnail_path),

            'members_count' => $this->whenCounted('approvedMembers'),


            // Use policies for permission checking
            'can' => $this->when(Auth::check(), function () {
                $user = Auth::user();
                return [
                    'update' => $this->isAdmin(),
                    'delete' =>  $this->isAdmin(),
                    'invite' =>  $this->isAdmin(),
                    'moderate' =>  $this->isAdmin(),
                    'post' =>  $this->isAdmin(),
                    'join' =>  $this->isNotMember(),
                    'leave' =>  $this->isApproved(),
                    'cancel' => $this->isPending(),
                    'viewMembers' =>  $this->isApproved() || $this->is_public,
                    'viewPendingRequests' =>  $this->isAdmin(),
                    'promoteMembers' => $this->isAdmin(),
                ];
            }),

            // Membership data
            'current_user' => $this->when(
                Auth::check() && $this->resource->relationLoaded('currentUserMembership'),
                function () {
                    $membership = $this->currentUserMembership;
                    return $membership ? [
                        'role' => $membership->role,
                        'status' => $membership->status,
                        'approved_at' => $membership->approved_at,
                        'is_admin' => $this->isAdmin(),
                        'is_owner' => $this->isOwner(),
                        'is_approved' => $this->isApproved(),
                        'is_pending' => $this->isPending(),
                        'is_invited' => $this->isInvited(),
                    ] : null;
                }
            ),

            $this->mergeWhen(
                $this->canSeeInvitedUsers(),
                fn() => [
                    'invited_users' => GroupMemberResource::collection($this->whenLoaded('invitedUsers')),

                ]
            ),
            $this->mergeWhen(
                $this->canSeeInvitedUsers(),
                fn() => [
                    'pending_requests' => MembershipResource::collection($this->whenLoaded('pendingRequests')),
                ]
            ),
        ];
    }

    protected function canSeeInvitedUsers(): bool
    {
        return $this->resource->relationLoaded('currentUserMembership')
            && $this->resource->currentUserMembership?->role === 'admin';
    }

    protected function isAdmin(): bool
    {
        return $this->resource->relationLoaded('currentUserMembership')
            && $this->resource->currentUserMembership?->role === UserRole::Admin->value;
    }

    public function isOwner()
    {
        return Auth::check() && Auth::id() === $this->creator_id;
    }

    public function isNotMember()
    {
        return $this->resource->relationLoaded('currentUserMembership')
            && !$this->resource->currentUserMembership;
    }

    public function isApproved()
    {
        return $this->resource->relationLoaded('currentUserMembership')
            && $this->resource->currentUserMembership?->status === MembershipStatus::Approved->value;
    }
    public function isPending()
    {
        return $this->resource->relationLoaded('currentUserMembership')
            && $this->resource->currentUserMembership?->status === MembershipStatus::Pending->value;
    }

    public function isInvited()
    {
        return $this->resource->relationLoaded('currentUserMembership')
            && $this->resource->currentUserMembership?->status === MembershipStatus::Invited->value;
    }
}
