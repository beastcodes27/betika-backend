<?php

namespace App\Http\Controllers\Tipster;

use App\Http\Controllers\Controller;
use App\Models\TipsterProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class TipsterProfileController extends Controller
{
    /**
     * Show tipster profile settings & avatar upload view.
     */
    public function edit()
    {
        $user = Auth::user();
        $tipster = $user->tipsterProfile ?? TipsterProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'display_name' => $user->name,
                'handle' => '@' . strtolower(str_replace(' ', '', $user->name)),
                'avatar_bg' => '#013220',
                'win_rate' => 75.0,
                'badge' => 'VERIFIED TIPSTER 🔥',
                'specialty' => 'European Football & Banker Slips',
                'is_verified' => true,
                'can_publish' => true,
                'status' => 'approved',
            ]
        );

        return view('tipster.profile', compact('tipster', 'user'));
    }

    /**
     * Update tipster details (bio, specialty, display name).
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $tipster = $user->tipsterProfile;

        $validated = $request->validate([
            'display_name' => 'required|string|max:100',
            'specialty' => 'required|string|max:150',
            'bio' => 'nullable|string|max:500',
        ]);

        $tipster->update($validated);

        return back()->with('success', 'Profile details updated successfully!');
    }

    /**
     * Upload and process tipster profile picture.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $user = Auth::user();
        $tipster = $user->tipsterProfile;

        if (!$tipster) {
            return back()->with('error', 'Tipster profile not found.');
        }

        $file = $request->file('avatar');
        $extension = $file->getClientOriginalExtension();
        $fileName = 'tipster_' . $tipster->id . '_' . time() . '.' . $extension;
        $uploadDir = public_path('uploads/avatars');

        if (!File::exists($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }

        // Delete previous avatar file if exists
        if ($tipster->avatar_path && File::exists(public_path($tipster->avatar_path))) {
            File::delete(public_path($tipster->avatar_path));
        }

        // Move to destination
        $file->move($uploadDir, $fileName);
        $relativePath = 'uploads/avatars/' . $fileName;

        $tipster->update([
            'avatar_url' => asset($relativePath),
            'avatar_path' => $relativePath,
            'avatar_mime' => $file->getClientMimeType() ?? ('image/' . $extension),
            'avatar_uploaded_at' => now(),
        ]);

        return back()->with('success', 'Profile picture uploaded and updated successfully!');
    }

    /**
     * Remove custom profile picture and reset to default initials.
     */
    public function removeAvatar()
    {
        $user = Auth::user();
        $tipster = $user->tipsterProfile;

        if ($tipster) {
            if ($tipster->avatar_path && File::exists(public_path($tipster->avatar_path))) {
                File::delete(public_path($tipster->avatar_path));
            }

            $tipster->update([
                'avatar_url' => null,
                'avatar_path' => null,
                'avatar_mime' => null,
                'avatar_uploaded_at' => null,
            ]);
        }

        return back()->with('success', 'Profile picture removed. Avatar reset to initial badge.');
    }
}
