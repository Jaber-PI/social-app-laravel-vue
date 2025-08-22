<?php

namespace App\Http\Controllers;

use App\Enums\MembershipStatus;

use App\Http\Requests\GroupInviteMemberRequest;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Resources\GroupMemberResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use App\Notifications\Group\GroupInvitation;
use App\Notifications\Group\GroupInviteApproved;
use App\Notifications\Group\GroupJoinRequest;
use App\Notifications\Group\GroupRequestApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
        $group->load(['approvedMembers', 'pendingRequests', 'pendingRequests.user', 'creator', 'currentUserMembership']);

        return Inertia::render('Groups/Show', [
            'group' => new GroupResource($group),
            'members' => GroupMemberResource::collection($group->approvedMembers)
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

    public function inviteMember(GroupInviteMemberRequest $request, Group $group)
    {
        $this->authorize('invite', $group);

        // Send invitation logic here

        $invitee = $request->invitee;

        $invite = GroupUser::create([
            'group_id' => $group->id,
            'user_id' => $invitee->id,
            'confirmation_token' => Str::random(16),
            'added_by' => $request->user()->id,
        ])->load('group', 'inviter');

        $invitee->notify(new GroupInvitation($invite));

        // $invite->user_id
        // ? $user->notify(new GroupInvitation($invite))
        // : Mail::to($request->email)->send(new GroupInviteMail($invite));


        return response()->json(['message' => 'Invitation sent successfully.']);
    }

    public function acceptInvitation(Group $group, $token)
    {
        $invite = GroupUser::where('confirmation_token', $token)->where('group_id', $group->id)->with('user')->firstOrFail();

        if (!$invite) {
            abort(404, 'Invitation not found');
        }

        if ($invite->status !== 'pending') {
            abort(403, 'Invitation no longer valid');
        }

        $invite->update(['status' => 'approved', 'approved_at' => now()]);

        $user = $invite->user;

        $group->admins->each(function ($admin) use ($group, $user) {
            $admin->notify(new GroupInviteApproved($group, $user));
        });

        return redirect()->route('groups.show', $invite->group->slug)
            ->with('success', 'You joined the group!');
    }

    public function requestToJoin(Group $group)
    {
        $this->authorize('requestToJoin', $group);

        $membership = GroupUser::firstOrCreate([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
        ], [
            'role' => 'member',
            'status' => $group->auto_approval ? 'approved' : 'pending',
            'confirmation_token' => Str::random(16),
            'added_by' => Auth::id(),
        ]);

        if ($membership->wasRecentlyCreated) {
            $group->admins->each(function ($admin) use ($group, $membership) {
                $admin->notify(new GroupJoinRequest($group, $membership->user));
            });
            return back()->with('success', 'Request to join group sent successfully.');
        }

        return back()->withErrors([
            'error' => 'You have already requested to join this group.'
        ]);
    }

    public function cancelRequest(Group $group)
    {
        $this->authorize('requestToJoin', $group);

        $membership = GroupUser::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($membership) {
            $membership->delete();
            return back()->with('success', 'Request to join group cancelled successfully.');
        }

        return back()->withErrors([
            'error' => 'You have no pending request to cancel.'
        ]);
    }


    public function leave(Group $group)
    {

        $membership = GroupUser::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($membership) {
            $membership->delete();
            return back()->with('success', 'You have left the group successfully.');
        }

        return back()->withErrors([
            'error' => 'You are not a member of this group.'
        ]);
    }

    // admin approve
    public function approveRequest(Group $group,  $requestID)
    {
        $this->authorize('update', $group);

        $membership = GroupUser::where('group_id', $group->id)
            ->where('id', $requestID)
            ->where('status', 'pending')
            ->first();

        if ($membership) {
            $membership->update(['status' => 'approved', 'approved_at' => now(), 'added_by' => Auth::id()]);
            $membership->user->notify(new GroupRequestApproved($group, $membership->user));
            return back()->with('success', 'Member approved successfully.');
        }

        return back()->withErrors([
            'error' => 'Member not found.'
        ]);
    }


    public function rejectRequest(Group $group, $requestID)
    {
        $this->authorize('update', $group);

        $membership = GroupUser::where('group_id', $group->id)
            ->where('id', $requestID)
            ->where('status', 'pending')
            ->first();

        if ($membership) {
            $membership->update(['status' => MembershipStatus::Rejected]);
            return back()->with('success', 'Member rejected successfully.');
        }

        return back()->withErrors([
            'error' => 'Member not found.'
        ]);
    }

    // admin remove
    public function removeMember(Group $group, User $user)
    {
        $this->authorize('update', $group);

        $membership = GroupUser::where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->first();

        if ($membership) {
            $membership->delete();
            return back()->with('success', 'Member removed successfully.');
        }

        return back()->withErrors([
            'error' => 'Member not found.'
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
