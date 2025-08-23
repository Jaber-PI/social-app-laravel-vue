<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    protected $table = 'group_users'; // pivot table
    protected $fillable = ['group_id', 'user_id', 'role', 'status', 'added_by', 'confirmation_token'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    function isAdmin()
    {
        return $this->role === UserRole::Admin->value;
    }
}
