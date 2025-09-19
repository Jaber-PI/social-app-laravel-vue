<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\UserPostResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{

    /**
     * Display the user's profile.
     */
    public function show(User $user, Request $request)
    {
        $user->loadCount(['followers', 'posts', 'comments']);
        $user->load('followers');

        $validTabs = ['posts', 'about', 'followers'];

        $currentTab = $request->query('tab', 'posts');
        // Validate tab parameter
        if (!in_array($currentTab, $validTabs)) {
            // Redirect to valid tab if invalid tab provided
            return redirect()->route('groups.show', [
                'user' => $user->id,
                'tab' => 'posts'
            ]);
        }

        /*
        * @var \App\Models\User|null $currentUser
        */
        $currentUser = Auth::user();

        return Inertia::render('Profile/Show', [
            'profile' => new UserResource($user),
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            "can" => [
                "follow" => $currentUser && $currentUser->id !== $user->id,
                "edit" => $currentUser && $currentUser->id === $user->id,
            ],
            'is_current_user' => $currentUser && $currentUser->id === $user->id,
            // check if the authenticated user is following the profile user
            'is_following' => $currentUser ? $currentUser->isFollowing($user->id) : false,
            'current_tab' => $currentTab,
        ]);
    }

    /**
    //  * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::back();
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // save cover and avatara image
    public function saveImage(Request $request, User $user)
    {
        $data = $request->validate([
            'cover' => ['image', 'nullable'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        if (isset($data['cover'])) {
            if ($user->cover_path != null) {
                Storage::disk('public')->delete($user->cover_path);
            }
            $file = $data['cover'];
            $fileName = time() . '.' . $file->extension();
            $dirName = 'images/covers/' . $user->id;
            $user->cover_path = $file->storeAs($dirName, $fileName, 'public');
            $user->save();

            return Redirect::back()->with('success', 'Avatar have been successfully Updated.');
        }
        if (isset($data['avatar'])) {
            if ($user->avatar_path != null) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $file = $data['avatar'];
            $fileName = time() . '.' . $file->extension();
            $dirName = 'images/avatars/' . $user->id;
            $user->avatar_path = $file->storeAs($dirName, $fileName, 'public');
            $user->save();
            return Redirect::back()->with('success', 'Operation completed successfully!');
            return response()->json(['success' => true, 'message' => 'Operation completed successfully!']);
        }
    }

    public function posts(User $user)
    {
        $posts = $user->posts()
            ->with(['author', 'attachments', 'reactedByAuthUser'])
            ->withCount('reactions', 'comments')
            ->whereNull('group_id')
            ->latest()
            ->cursorPaginate(5)
            ->withQueryString();

        return UserPostResource::collection($posts);
    }

    public function followers(User $user)
    {
        $followers = $user->followers()->with('follower')->latest()->cursorPaginate(5);

        return UserResource::collection($followers);
    }
}
