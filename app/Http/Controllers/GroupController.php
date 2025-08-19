<?php

namespace App\Http\Controllers;

use App\Enums\MembershipStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Models\Group;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Str;
use Inertia\Inertia;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $groups = Group::with(['creator'])
            ->latest()
            ->withExists(['members as is_member' => function ($q) use ($request) {
                $q->whereKey($request->user()->id);
            }])
            ->get();
        return GroupResource::collection($groups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request)
    {

        Gate::authorize('create', Group::class);

        $validated = $request->validated();

        $coverPath = $request->hasFile('cover')
            ? $request->file('cover')->store('groups/covers', 'public')
            : null;

        $thumbnailPath = $request->hasFile('thumbnail')
            ? $request->file('thumbnail')->store('groups/thumbnails', 'public')
            : null;



        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'cover_path' => $coverPath,
            'thumbnail_path' => $thumbnailPath,
            'auto_approval' => $validated['auto_approval'] ?? true,
            'slug' => Str::slug($validated['name']) . '-' . uniqid(),
            'created_by' => $request->user()->id,
        ]);

        $group->members()->attach($request->user()->id, [
            'role' => 'admin',
            'approved_at' => now(),
            'status' => 'approved',
            'added_by' => $request->user()->id,
        ]);

        $group->user_role = 'admin';
        $group->user_status = 'approved';
        $group->user_approved_at = now();

        return response()->json(new GroupResource($group), 201);
        // return redirect()->route('groups.show', $group);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        return Inertia::render('Groups/Show', [
            'group' => new GroupResource($group->load(['members', 'creator'])),
        ]);
    }

    public function posts(Group $group)
    {

        $posts = $group->posts()->with('author', 'attachments', 'reactedByAuthUser')->withCount('reactions', 'comments')
            ->latest()
            ->cursorPaginate(5)
            ->withQueryString();

        return PostResource::collection($posts);
    }
}
