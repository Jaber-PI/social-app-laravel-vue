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
            'members' => UserResource::collection($this->whenLoaded('members')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'posts' => PostResource::collection($this->whenLoaded('posts')),
            'is_member' => $this->when(Auth::check(), function () {
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
