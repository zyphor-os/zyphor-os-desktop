<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProfileController extends Controller
{
    private function currentUser(): User
    {
        $userName = trim(shell_exec('whoami'));

        return User::firstOrCreate(
            ['username' => $userName],
            ['avatar' => null]
        );
    }

    // api: json info
    public function getUserInfo()
    {
        $user = $this->currentUser();

        return response()->json([
            'id'       => $user->id,
            'username' => $user->username,
            'name'     => $user->name,
            'email'    => $user->email,
            'bio'      => $user->bio,
            'avatar'   => $user->avatar ? Storage::disk('public')->url($user->avatar) : null,
        ]);
    }

    // view
    public function getUserName()
    {
        $user = $this->currentUser();

        return view('profile', [
            'userName' => $user->username,
            'userInfo' => [
                'name'   => $user->name,
                'email'  => $user->email,
                'bio'    => $user->bio,
                'avatar' => $user->avatar ? Storage::disk('public')->url($user->avatar) : null,
            ],
        ]);
    }

    // api: save profile fields
    public function updateProfile(Request $request)
    {
        $user = $this->currentUser();

        $validated = $request->validate([
            'name'  => ['nullable', 'string', 'max:40'],
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'bio'   => ['nullable', 'string', 'max:500'],
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile saved',
            'user'    => $user,
        ]);
    }

    // api: upload avatar
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $user = $this->currentUser();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return response()->json([
            'message' => 'Avatar updated',
            'avatar'  => Storage::disk('public')->url($path),
        ]);
    }

    // api: remove avatar
    public function removeAvatar()
    {
        $user = $this->currentUser();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return response()->json(['message' => 'Avatar removed']);
    }
}
