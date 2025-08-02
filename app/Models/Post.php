<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    //
    use HasFactory;
    use SoftDeletes;


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
        return $this->hasMany(Comment::class);
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

    public function reactions()
    {
        return $this->hasMany(PostReaction::class);
    }

    public function isReactedBy(User $user): bool
    {
        return $this->reactions()->where('user_id', $user->id)->exists();
    }
}
