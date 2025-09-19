<?php

namespace App\Http\Controllers;

use App\Actions\Groups\CreateGroupAction;
use App\Enums\MembershipStatus;
use App\Enums\UserRole;
use App\Http\Requests\GroupInviteMemberRequest;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupMemberResource;
use App\Http\Resources\GroupPostResource;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Models\GroupUser;
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
use Illuminate\Validation\Rules\Enum;

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
    public function show(Group $group, Request $request)
    {

        // / Define valid tabs
        $validTabs = ['posts', 'about', 'members'];

        // Get tab from URL parameter, default to 'posts'
        $currentTab = $request->query('tab', 'posts');

        // Validate tab parameter
        if (!in_array($currentTab, $validTabs)) {
            // Redirect to valid tab if invalid tab provided
            return redirect()->route('groups.show', [
                'group' => $group->id,
                'tab' => 'posts'
            ]);
        }


        $group->load(['approvedMembers', 'pendingRequests', 'pendingRequests.user', 'creator', 'currentUserMembership'])->loadCount('approvedMembers');

        return Inertia::render('Groups/Show', [
            'group' => new GroupResource($group),
            'members' => GroupMemberResource::collection($group->approvedMembers),
            'currentTab' => $currentTab,
            'validTabs' => $validTabs,   // Pass valid tabs for client validation
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request, CreateGroupAction $createGroup)
    {
        Gate::authorize('create', Group::class);

        $group = $createGroup->execute(
            $request->user(),
            $request->only(['name', 'description', 'auto_approval']),
            $request->file('cover'),
            $request->file('thumbnail')
        );;

        return response()->json(new GroupResource($group->load('currentUserMembership')), 201);
    }

    public function update(Group $group, UpdateGroupRequest $request)
    {
        $this->authorize('update', $group);

        $group->update($request->only(['name', 'description', 'auto_approval']));

        return back()->with('success', 'Group updated successfully.');
    }


    public function inviteMember(GroupInviteMemberRequest $request, Group $group)
    {
        $this->authorize('invite', $group);

        // Send invitation logic here


        $invitee = $request->invitee;

        $invite = GroupUser::create([
            'status' => MembershipStatus::Invited,
            'group_id' => $group->id,
            'user_id' => $invitee->id,
            'confirmation_token' => Str::random(16),
            'added_by' => $request->user()->id,
        ])->load('group', 'inviter');

        $invitee->notify(new GroupInvitation($invite));

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
        $this->authorize('join', $group);

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
            return back()->with(['success' => 'Request to join group sent successfully.']);
        }

        return back()->withErrors(['error' => 'You have already requested to join this group.'], 409);
    }

    // cancel request
    public function cancelRequest(Group $group)
    {

        $membership = GroupUser::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($membership) {
            $membership->delete();
            return back()->with('success', 'Request to join group cancelled successfully.');
        }

        return back()->withErrors(['error' => 'You have no pending request to cancel.'], 404);
    }


    public function leave(Group $group)
    {
        $detached = $group->members()->detach(Auth::id());

        if ($detached) {
            return back()->with('success', 'You have left the group successfully.');
        }

        return back()->withErrors(['error' => 'You are not a member of this group.'], 404);
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
    public function removeMember(Group $group, Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'numeric'],
        ]);

        $this->authorize('update', $group);

        $membership = GroupUser::where('group_id', $group->id)
            ->where('user_id', $data['user_id'])
            ->first();

        if ($membership) {
            $membership->delete();
            return back()->with('success', 'Member removed successfully.');
        }

        return back()->withErrors([
            'error' => 'Member not found.'
        ]);
    }

    public function changeRole(Group $group, Request $request)
    {
        $this->authorize('update', $group);

        $data = $request->validate([
            'role' => ['required', new Enum(UserRole::class)],
            'user_id' => ['required', 'numeric'],
        ]);

        $membership = GroupUser::where('group_id', $group->id)
            ->where('user_id', $data['user_id'])
            ->first();

        if (!$membership) {
            return back()->withErrors([
                'error' => 'Member not found.'
            ]);
        }
        // Prevent admin from changing their own role
        if ($membership->isAdmin() && Auth::id() == $data['user_id']) {
            return back()->withErrors([
                'error' => 'You cannot change your own role as an admin.'
            ]);
        }

        $membership->update(['role' => $data['role']]);
        return back()->with('success', 'Member role updated successfully.');
    }

    public function posts(Group $group)
    {
        $posts = $group->posts()
        ->with(['author', 'attachments', 'reactedByAuthUser', 'group', 'group.currentUserMembership'])->withCount('reactions', 'comments')
            ->latest()
            ->cursorPaginate(5)
            ->withQueryString();

        return GroupPostResource::collection($posts);
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
            $dirName = 'images/groups/covers/' . $group->id;
            $group->cover_path = $file->storeAs($dirName, $fileName, 'public');
            $group->save();

            // return redirect()->back()->with('success', 'Cover image has been successfully updated.');
            return response()->json([
                'success' => true,
                'message' => 'Cover image updated successfully!',
                'cover_path' => $group->cover_path, // send new image URL if needed
            ]);
        }
        if (isset($data['avatar'])) {
            if ($group->thumbnail_path != null) {
                Storage::disk('public')->delete($group->thumbnail_path);
            }

            $file = $data['avatar'];
            $fileName = time() . '.' . $file->extension();
            $dirName = 'images/groups/avatars/' . $group->id;
            $group->thumbnail_path = $file->storeAs($dirName, $fileName, 'public');
            $group->save();
            // return redirect()->back()->with('success', 'Operation completed successfully!');
            return response()->json(['success' => true, 'message' => 'Operation completed successfully!']);
        }
    }
}
