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

    public function invitedUsers()
    {
        return $this->belongsToMany(User::class, 'group_users')
            ->withPivotValue('status', 'invited')
            ->withPivot('added_by', 'confirmation_token')
            ->withTimestamps();
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_users')->withPivot('role', 'approved_at', 'status', 'added_by')
            ->withTimestamps();
    }

    public function admins()
    {
        return $this->belongsToMany(User::class, 'group_users')->wherePivot('role', 'admin')->withPivot('role', 'approved_at', 'status', 'added_by')
            ->withTimestamps();
    }

    public function approvedMembers()
    {
        return $this->belongsToMany(User::class, 'group_users')->wherePivot('status', 'approved')->withPivot('role', 'approved_at', 'status', 'added_by')
            ->withTimestamps();
    }

    public function pendingRequests()
    {
        return $this->hasMany(GroupUser::class)->where('status', 'pending');
    }

    public function memberships()
    {
        return $this->hasMany(GroupUser::class);
    }

    public function currentUserMembership()
    {
        return $this->hasOne(GroupUser::class)->where('user_id', Auth::id());
    }

    public function membershipForUser($userId)
    {
        return $this->hasOne(GroupUser::class)->where('user_id', $userId);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
