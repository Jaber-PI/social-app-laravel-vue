<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FollowerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'name' => $this->name,
            'username' => $this->username,
            'avatar_url' => $this->avatar_path ? Storage::url($this->avatar_path) : '/images/monir.jpeg',
            'followers_count' => $this->whenCounted('followers'),
            'posts_count' => $this->whenCounted('posts'),

        ];
    }
}
