<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GroupMemberResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'avatar_url' => $this->avatar_path ? Storage::url($this->avatar_path) : '/images/monir.jpeg',
            'cover_url' => $this->cover_path ? Storage::url($this->cover_path) : null,

            // Pivot fields (from group_user pivot table)
            'role'     => $this->pivot?->role,
            'status'   => $this->pivot?->status,
            'joined_at' => $this->pivot?->approved_at,
            'added_by'  => $this->pivot?->added_by,

            'isCurrentUser' => $this->id === Auth::id(),
            'isAdmin' => $this->pivot?->status === 'approved' && $this->pivot?->role === 'admin',

            'inviter' => $this->whenLoaded('inviter', function () {
                return [
                    'id' => $this->inviter->id,
                    'name' => $this->inviter->name,
                    'avatar_url' => $this->inviter->avatar_path ? Storage::url($this->inviter->avatar_path) : '/images/monir.jpeg',
                ];
            }),

            'confirmation_token' => $this->pivot?->confirmation_token,
        ];
    }
}
