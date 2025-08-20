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

            'creator' => new UserResource($this->whenLoaded('creator')),

            'cover_path' => $this->cover_path,
            'avatar_path' => $this->avatar_path,

            // Membership info for the authenticated user
            'current_user' => $this->whenLoaded('currentUserMembership', function () {
                $membership = $this->currentUserMembership;
                return $membership ? [
                    'role'        => $membership->role,
                    'status'      => $membership->status,
                    'approved_at' => $membership->approved_at,
                ] : null;
            }),

            $this->mergeWhen(
                $this->resource->relationLoaded('currentUserMembership'),
                fn() => [
                    'can' => [
                        'update' => $this->currentUserMembership?->role === 'admin',
                        'delete' => $this->currentUserMembership?->role === 'admin',
                        'invite' => $this->currentUserMembership?->role === 'admin',
                    ],
                ]
            ),

        ];
    }
}


