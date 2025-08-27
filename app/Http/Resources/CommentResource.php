<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommentResource extends JsonResource
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
            'body' => $this->body,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'reacted_by_user' => $this->relationLoaded('reactedByAuthUser') && $this->reactedByAuthUser !== null,
            'reactions_count' => $this->whenCounted('reactions'),

            'comments_count' => $this->whenCounted('comments'),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'avatar_url' => $this->user->avatar_path ? Storage::url($this->user->avatar_path) : '/images/monir.jpeg',
                ];
            }),
            'can' => [
                'delete' => Auth::id() === $this->created_by || $this->isParentOwner(),
                'update' => Auth::id() === $this->created_by,
                'comment' => $this->commentable_type !== Comment::class,
            ],
        ];
    }

    protected function isParentOwner()
    {
        return $this->relationLoaded('commentable') &&
            $this->commentable->created_by === Auth::id();
    }
}
