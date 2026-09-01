<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\{
    Storage, Auth
};
use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('pages.profile.edit');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'bio' => ['nullable', 'string', 'max:5000'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->bio = $validated['bio'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $validated = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $this->replaceImage(
            Auth::user(),
            'avatar',
            $validated['avatar'],
            'users/avatar'
        );

        return back()->with('success', 'Profile picture updated successfully.');
    }

    public function removeAvatar()
    {
        $this->deleteImage(Auth::user(), 'avatar');

        return back()->with('success', 'Profile picture removed.');
    }

    public function updateBanner(Request $request)
    {
        $validated = $request->validate([
            'banner' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $this->replaceImage(
            Auth::user(),
            'banner',
            $validated['banner'],
            'users/banner'
        );

        return back()->with('success', 'Banner image updated successfully.');
    }

    public function removeBanner()
    {
        $this->deleteImage(Auth::user(), 'banner');

        return back()->with('success', 'Banner image removed.');
    }

    private function replaceImage(User $user, string $field, $file, string $path): void
    {
        if ($old = $user->{$field}) {
            Storage::disk('upload')->delete($old);
        }

        $user->forceFill([$field => $file->store($path, 'upload')])->save();
    }

    private function deleteImage(User $user, string $field): void
    {
        if ($old = $user->{$field}) {
            Storage::disk('upload')->delete($old);
            $user->forceFill([$field => null])->save();
        }
    }
}
