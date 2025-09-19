<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function follow(User $user)
    {
        Follower::create([
            'user_id' => $user->id,
            'follower_id' => Auth::id(),
            'created_ar' => now(),
        ]);
        return back()->with('success', 'You are now following ' . $user->name);
    }

    public function unfollow(User $user)
    {
        $user->followers()->detach(Auth::id());
        return back()->with('success', 'You are no longer following ' . $user->name);
    }
}
