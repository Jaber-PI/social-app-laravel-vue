<?php

namespace App\Http\Controllers;

use App\Enums\ReactionType;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Services\ReactionService;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $request->validate([
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|integer',
        ]);

        $modelClass = $request->commentable_type;

        abort_unless(class_exists($modelClass), 404);

        $comments = Comment::where('commentable_type', $modelClass)
            ->where('commentable_id', $request->commentable_id)
            ->with(['user:id,name,avatar_path', 'commentable:created_by', 'reactedByAuthUser'])
            ->withCount(['reactions', 'comments'])
            ->latest()
            ->get();

        return response()->json(CommentResource::collection($comments));
    }

    public function store(Request $request)
    {
        $request->validate([
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|integer',
            'body' => 'required|string|max:5000',
        ]);

        $modelClass = $request->commentable_type;

        abort_unless(class_exists($modelClass), 404);

        $commentable = $modelClass::findOrFail($request->commentable_id);

        $comment = $commentable->comments()->create([
            'body' => $request->body,
            'created_by' => $request->user()->id
        ]);

        return response()->json(new CommentResource($comment->load('user:id,name')), 201);
    }



    public function update(Request $request, Comment $comment)
    {


        $this->authorize('update', $comment);
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment->update(['body' => $request->body]);
        return response()->json(new CommentResource($comment->load('user:id,name')));
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();
        return response()->json(['message' => 'Comment deleted.']);
    }


    public function react(Request $request, Comment $comment, ReactionService $reactionService)
    {

        Gate::authorize('react', $comment);
        $result = $reactionService->toggleReaction($comment, $request->user());
        return response()->json($result);
    }
}
