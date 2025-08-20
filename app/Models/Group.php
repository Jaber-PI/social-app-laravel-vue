<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Group extends Model
{

    use HasSlug;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    protected $fillable = [
        'name',
        'description',
        'cover_path',
        'thumbnail_path',
        'auto_approval',
        'created_by',
    ];

    protected $casts = [
        'auto_approval' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_users')->withPivot('role', 'approved_at', 'status', 'added_by')
            ->withTimestamps();
    }

    public function memberships()
    {
        return $this->hasMany(GroupMember::class);
    }


    public function currentUserMembership()
    {
        return $this->hasOne(GroupMember::class)->where('user_id', Auth::id());
    }

    public function membershipForUser($userId)
    {
        return $this->hasOne(GroupMember::class)->where('user_id', $userId);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
