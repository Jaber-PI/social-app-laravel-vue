<?php

namespace App\Http\Controllers;

use App\Enums\ReactionType;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class PostReactionController extends Controller
{


    public function react(Request $request, Post $post)
    {
        // $validated = $request->validate([
        //     'post_id' => ['required', 'exists:posts,id'],
        //     'reaction_type' => ['required', new Enum(ReactionType::class)],
        // ]);

        $user = $request->user();

        $reaction = $post->reactions()->where('user_id', $user->id)->first();

        if ($reaction) {
            $reaction->delete();
            return response()->json(['reacted' => false, 'reactions_count' => $post->reactions()->count()]);
        } else {
            $post->reactions()->create(['user_id' => $user->id]);
            return response()->json(['reacted' => true, 'reactions_count' => $post->reactions()->count()]);
        }
    }
}
