<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;


class GroupResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'members' => GroupMemberResource::collection($this->whenLoaded('members')),
            'creator' => new GroupMemberResource($this->whenLoaded('creator')),
            'cover_path' => $this->cover_path,
            'avatar_path' => $this->avatar_path,

            'user_role' => $this->user_role ?? $this->pivot?->role,
            'user_status' => $this->user_status ?? $this->pivot?->status,
            'user_approved_at' => $this->user_approved_at ?? $this->pivot?->approved_at,

            'is_member' => $this->when(
                Auth::check(),
                function () {
                    // Case 1: is_member was loaded via withExists / withCount
                    if ($this->relationLoaded('is_member') || isset($this->is_member)) {
                        return (bool) $this->is_member;
                    }

                    // Case 2: members were eager-loaded (like in show)
                    if ($this->relationLoaded('members')) {
                        return $this->members->contains(Auth::id());
                    }

                    // Fallback: run a direct exists query (only if needed)
                    return $this->members()->whereKey(Auth::id())->exists();
                }
            ),
        ];
    }
}
