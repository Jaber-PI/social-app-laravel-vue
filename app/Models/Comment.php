<?php

namespace App\Models;

use App\Traits\HasReactions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{

    use HasReactions;

    protected $fillable = ['post_id', 'created_by', 'body'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }


    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    protected static function booted()
    {
        static::deleting(function ($comment) {
            // Delete all reactions for this comment
            $comment->reactions()->delete();

            // Recursively delete replies and their reactions
            foreach ($comment->comments as $reply) {
                $reply->delete(); // triggers deleting() on reply as well
            }
        });
    }
}
