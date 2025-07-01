<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PostAttachment extends Model
{
    //

    CONST UPDATED_AT = null;

    protected $fillable = [
        'post_id',     // If you want to mass assign the relation
        'filename',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    protected static function booted()
    {
        static::deleting(function ($attachment) {
            if ($attachment->file_path) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        });
    }
}
