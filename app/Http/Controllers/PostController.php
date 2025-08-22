<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Services\ReactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('author', 'group', 'attachments', 'reactedByAuthUser')->withCount('reactions', 'comments')
            ->latest()
            ->cursorPaginate(5)
            ->withQueryString();

        return PostResource::collection($posts);
    }


    public function show(Request $request, Post $post)
    {
        if ($request->wantsJson()) {
            return response()->json(new PostResource($post->load('author', 'attachments', 'reactedByAuthUser')->loadCount('reactions', 'comments')));
        }
        return;
    }

    public function latest(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(new PostResource(Post::with('author', 'attachments', 'reactedByAuthUser')->withCount('reactions', 'comments')
                ->latest()->first()));
        }

        return;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        $allFilesPaths = [];

        try {
            $attachments = $data['attachments'];
            unset($data['attachments']);
            $post = Auth::user()->posts()->create($data);
            $allFilesPaths = $post->addAttachments($attachments);
            DB::commit();

            // if ($request->wantsJson()) {
            //     return response()->json(new PostResource($post->load('u', 'attachments')));
            // }

            if ($request->wantsJson()) {
                return response()->json($post->load('author', 'attachments'));
            }

            return redirect()->back()->with('success', 'post created');
        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($allFilesPaths as $path) {
                Storage::disk('public')->delete($path);
            }
            // throw $e;
            return redirect()->back()->with('error', 'post not created');
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $attachments = $data['attachments'];
            $deleted_attachments = $data['deleted_attachments'];
            unset($data['attachments'], $data['deleted_attachments']);
            $post->update($data);
            if ($attachments) {
                $post->addAttachments($attachments);
            }
            foreach ($deleted_attachments as $attachment) {
                PostAttachment::destroy($attachment);
            }
            DB::commit();
            return redirect()->back()->with('success', 'post updated');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            return redirect()->back()->with('error', 'post not updatedd');
        }
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (Auth::id() !== $post->created_by) {
            abort(403, 'Permission denied.');
        }

        $post->delete();
    }


    public function react(Request $request, Post $post, ReactionService $reactionService)
    {
        // Gate::authorize('react', $post);
        $result = $reactionService->toggleReaction($post, $request->user());
        return response()->json($result);
    }

    public function downloadAttachment(PostAttachment $attachment)
    {
        return Storage::disk('public')->download($attachment->file_path, $attachment->filename);
    }
}
