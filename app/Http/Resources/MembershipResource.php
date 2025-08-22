<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MembershipResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'group_id' => $this->group_id,
            'user_id' => $this->user_id,
            'role' => $this->role,
            'status' => $this->status,
            'added_by' => $this->added_by,
            'confirmation_token' => $this->confirmation_token,
            'inviter' => new UserResource($this->whenLoaded('inviter')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
