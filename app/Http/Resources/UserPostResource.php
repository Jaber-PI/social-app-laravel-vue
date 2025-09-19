<?php

namespace App\Http\Resources;

use App\Enums\MembershipStatus;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body ?: '',
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'reactions_count' => $this->whenCounted('reactions'),
            'comments_count' => $this->whenCounted('comments'),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'author' => new UserResource($this->whenLoaded('author')),

            'attachments' => PostAttachmentResource::collection($this->whenLoaded('attachments')),

            'can' => $this->when(Auth::check(), $this->getPermissions()),
            'user_relationship' => $this->when(Auth::check(), $this->getUserRelationship()),

        ];
    }

    /**
     * Get user permissions for this post.
     */
    private function getPermissions(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return [
            'view' => true,
            'update' =>  $this->created_by == Auth::id(),
            'delete' => $this->group_id ? $this->isAdmin() : $this->created_by == Auth::id(),
            'react' => $this->group_id ? $this->isMember() : Auth::check(),
            'comment' => $this->group_id ? $this->isMember() : Auth::check(),
        ];
    }

    /**
     * Get user's relationship to this post.
     */
    private function getUserRelationship(): array
    {
        $user = Auth::user();

        return [
            'is_author' => $this->created_by === $user->id,
            'has_reacted' => $this->relationLoaded('reactedByAuthUser') && $this->reactedByAuthUser !== null,
        ];
    }

    protected function isMember(): bool
    {
        return $this->relationLoaded('group') && $this->group?->relationLoaded('currentUserMembership')
            && $this->group?->currentUserMembership?->status === MembershipStatus::Approved->value;
    }

    protected function isAdmin(): bool
    {
        return $this->relationLoaded('group') && $this->group?->relationLoaded('currentUserMembership')
            && $this->group?->currentUserMembership?->role === UserRole::Admin->value;
    }
}
