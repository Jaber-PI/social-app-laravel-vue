<?php

namespace App\Models;

use App\Enums\ReactionType;
use Illuminate\Database\Eloquent\Model;

class PostReaction extends Model
{
    protected $fillable = ['user_id', 'post_id', 'reaction_type'];

    protected $casts = [
        'reaction_type' => ReactionType::class,
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
