<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostResource extends JsonResource
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
            'reacted_by_user' => auth()->check() ? $this->reactions->isNotEmpty() : false,
            'reactions_count' => $this->whenCounted('reactions'),
            'comments_count' => $this->whenCounted('comments'),
            'author' => new UserResource($this->whenLoaded('author')),
            'group' => new GroupResource($this->whenLoaded('group')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'attachments' => PostAttachmentResource::collection($this->whenLoaded('attachments')),

        ];
    }
}
