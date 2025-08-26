<?php

namespace App\Models;

use App\Traits\HasReactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    use HasReactions;


    protected $guarded = [];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }


    public function attachments()
    {
        return $this->hasMany(PostAttachment::class);
    }

    public function addAttachments($attachments)
    {
        foreach ($attachments as $attachment) {
            $allFilesPaths = [];
            $path = $attachment->store("attachments/posts/{$this->id}", 'public');
            $allFilesPaths[] = $path;
            // Optionally save info in DB
            $this->attachments()->create([
                'filename' => $attachment->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $attachment->getMimeType(),
                'file_size' => $attachment->getSize(),
            ]);
        }
        return $allFilesPaths ?? 0;
    }

    public function scopeForUser($query, $userId = null)
    {

        $userId = $userId ?? Auth::id();

        $userGroupsIds = GroupUser::where('user_id', $userId)
            ->where('status', 'approved')
            ->pluck('group_id');

        return $query->where(function ($q) use ($userId, $userGroupsIds) {
            $q->whereNull('group_id')
                ->orWhereIn('group_id', $userGroupsIds);
        });
    }

    protected static function booted()
    {
        static::deleting(function ($post) {
            $post->reactions()->delete();

            foreach ($post->comments as $comment) {
                $comment->delete(); // triggers deleting() on comment as well
            }
        });
    }
}
