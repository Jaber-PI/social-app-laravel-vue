<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
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
    public function show(User $user): Response
    {
        return Inertia::render('Profile/Show', [
            'profile' => new UserResource($user),
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            "can" => [
                "connect" => Auth::user() ? true : false,
                "edit" => Auth::user()?->is($user) ? true : false,
            ]
        ]);
    }

    /**
     * Display the user's profile form.
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

            return response()->json(['success' => 'Cover have been successfully Updated.']);
        }
        if (isset($data['avatar'])) {
            $file = $data['avatar'];
            $fileName = time() . '.' . $file->extension();
            $dirName = 'images/avatars/' . $user->id;
            $user->avatar_path = $file->storeAs($dirName, $fileName);
            $user->save();
            return response()->json(['success' => 'Avatar have been successfully Updated.']);
        }
    }
}
