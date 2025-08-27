<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Group;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Notifications\PostCreatedNotification;
use App\Services\ReactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class PostController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $posts = Post::with(['author', 'group', 'group.currentUserMembership', 'attachments', 'reactedByAuthUser'])
            ->withCount('reactions', 'comments')
            ->forUser()
            ->latest()
            ->cursorPaginate(5)
            ->withQueryString();

        return PostResource::collection($posts);
    }


    public function show(Request $request, Post $post)
    {
        $post->load('author', 'group', 'group.currentUserMembership', 'attachments', 'reactedByAuthUser')->loadCount('reactions', 'comments');

        if ($request->wantsJson()) {
            return response()->json(new PostResource($post));
        }

        return Inertia::render('Posts/Show', [
            'post' => new PostResource($post)
        ]);
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
        $this->authorize('create', Post::class);

        $data = $request->validated();

        if ($request->group_id) {
            $group = Group::find($request->group_id);
            Gate::authorize('post', $group);
        }

        DB::beginTransaction();
        $allFilesPaths = [];

        try {
            $attachments = $data['attachments'];
            unset($data['attachments']);
            $post = $request->user()->posts()->create($data);
            $allFilesPaths = $post->addAttachments($attachments);
            DB::commit();

            // notify group users
            if ($request->group_id) {
                Notification::send($group->approvedMembers, new PostCreatedNotification($post, $request->user(), $group));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($allFilesPaths as $path) {
                Storage::disk('public')->delete($path);
            }
            // throw $e;
            return redirect()->back()->with('error', 'post not created');
        }


        if ($request->wantsJson()) {
            return response()->json($post->load('author', 'attachments'));
        }

        return redirect()->back()->with('success', 'post created');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {

        $this->authorize('update', $post);

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

        $this->authorize('delete', $post);

        $post->delete();
    }


    public function react(Request $request, Post $post, ReactionService $reactionService)
    {
        $this->authorize('react', $post);

        $result = $reactionService->toggleReaction($post, $request->user());

        return response()->json($result);
    }

    public function downloadAttachment(PostAttachment $attachment)
    {
        return response()->download(Storage::disk('public')->path($attachment->file_path), $attachment->filename);
    }
}
