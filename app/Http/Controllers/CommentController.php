<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function index(Post $post)
    {
        return response()->json(CommentResource::collection($post->comments()->with('user:id,name')->get()));
    }

    public function store(Request $request, Post $post)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);
        $comment = $post->comments()->create([
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ]);

        return response()->json(new CommentResource($comment->load('user:id,name')));
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment); // Checks if user can update

        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment->update(['body' => $request->body]);

        return response()->json(new CommentResource($comment->load('user:id,name')));
    }

    /**
     * Delete a comment.
     */


    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment); // Checks if user can delete

        $comment->delete();

        return response()->json(['message' => 'Comment deleted.']);
    }
}
