<?php

namespace App\Http\Controllers;

use App\Enums\MembershipStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Resources\GroupMemberResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Models\Group;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GroupController extends Controller
{
    use AuthorizesRequests;
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
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $group->load(['members', 'creator', 'currentUserMembership']);

        return Inertia::render('Groups/Show', [
            'group' => new GroupResource($group),
            'members' => GroupMemberResource::collection($group->members)
        ]);
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

        $group->load('currentUserMembership');

        return response()->json(new GroupResource($group), 201);
        // return redirect()->route('groups.show', $group);
    }



    public function posts(Group $group)
    {

        $posts = $group->posts()->with('author', 'attachments', 'reactedByAuthUser')->withCount('reactions', 'comments')
            ->latest()
            ->cursorPaginate(5)
            ->withQueryString();

        return PostResource::collection($posts);
    }


    // save cover and avatara image
    public function saveImage(Request $request, Group $group)
    {

        $this->authorize('update', $group);

        $data = $request->validate([
            'cover' => ['image', 'nullable'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        if (isset($data['cover'])) {
            if ($group->cover_path != null) {
                Storage::disk('public')->delete($group->cover_path);
            }
            $file = $data['cover'];
            $fileName = time() . '.' . $file->extension();
            $dirName = 'images/covers/' . $group->id;
            $group->cover_path = $file->storeAs($dirName, $fileName, 'public');
            $group->save();

            // return redirect()->back()->with('success', 'Cover image has been successfully updated.');
            return response()->json([
                'success' => true,
                'message' => 'Cover image updated successfully!',
                'cover_path' => $group->cover_path, // send new image URL if needed
            ]);
        }
        // if (isset($data['avatar'])) {
        //     if ($group->avatar_path != null) {
        //         Storage::disk('public')->delete($group->avatar_path);
        //     }

        //     $file = $data['avatar'];
        //     $fileName = time() . '.' . $file->extension();
        //     $dirName = 'images/avatars/' . $group->id;
        //     $group->avatar_path = $file->storeAs($dirName, $fileName, 'public');
        //     $group->save();
        //     // return redirect()->back()->with('success', 'Operation completed successfully!');
        //     return response()->json(['success' => true, 'message' => 'Operation completed successfully!']);
        // }
    }
}
