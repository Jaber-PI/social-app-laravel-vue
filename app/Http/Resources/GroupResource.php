<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;


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

            'is_public' => false,

            'creator' => new UserResource($this->whenLoaded('creator')),

            'cover_path' => $this->cover_path,
            'avatar_path' => $this->avatar_path,

            'members_count' => $this->whenCounted('approvedMembers'),

            $this->mergeWhen(
                $this->resource->relationLoaded('currentUserMembership'),
                function () {
                    $membership = $this->currentUserMembership;
                    return [
                        'can' => [
                            'update' => $membership?->role === 'admin',
                            'delete' => $membership?->role === 'admin',
                            'invite' => $membership?->role === 'admin',
                            'approve' => true,
                            'post' => $membership?->status === 'approved',
                            'view' => $membership?->status === 'approved',
                            'join' => !$membership,
                            'leave' => $membership?->status === 'approved',
                            'cancel' => $membership?->status === 'pending'
                        ],
                        'current_user' => $membership ? [
                            'role'        => $membership->role,
                            'status'      => $membership->status,
                            'approved_at' => $membership->approved_at,
                            'isAdmin' => $membership->role === 'admin',
                            'isOwner' => $membership->user_id === $this->creator_id,
                        ] : null,
                    ];
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
            && $this->currentUserMembership?->role === 'admin';
    }
}
