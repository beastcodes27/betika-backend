<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipsterProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TipsterController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = TipsterProfile::with(['user', 'betslips']);

        if ($status && in_array($status, ['pending', 'approved', 'suspended'])) {
            $query->where('status', $status);
        }

        $tipsters = $query->latest()->paginate(15);

        return view('admin.tipsters.index', compact('tipsters', 'status'));
    }

    public function edit(TipsterProfile $tipster): View
    {
        $tipster->load(['user', 'betslips.bookmaker']);
        return view('admin.tipsters.edit', compact('tipster'));
    }

    public function update(Request $request, TipsterProfile $tipster): RedirectResponse
    {
        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:255'],
            'specialty' => ['required', 'string', 'max:255'],
            'win_rate' => ['required', 'numeric', 'between:0,100'],
            'badge' => ['nullable', 'string', 'max:50'],
            'streak' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:pending,approved,suspended'],
            'is_verified' => ['boolean'],
            'can_publish' => ['boolean'],
            'approval_notes' => ['nullable', 'string'],
        ]);

        $validated['is_verified'] = $request->boolean('is_verified');
        $validated['can_publish'] = $request->boolean('can_publish');

        if ($validated['status'] === 'approved' && ! $tipster->approved_at) {
            $validated['approved_at'] = now();
            $validated['approved_by'] = auth()->id();
        }

        $tipster->update($validated);

        return redirect()->route('admin.tipsters.index')->with('success', "Tipster {$tipster->display_name} updated successfully.");
    }

    public function approve(TipsterProfile $tipster): RedirectResponse
    {
        $tipster->update([
            'status' => 'approved',
            'can_publish' => true,
            'is_verified' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', "Tipster {$tipster->display_name} approved! They can now publish betslips.");
    }

    public function suspend(TipsterProfile $tipster): RedirectResponse
    {
        $tipster->update([
            'status' => 'suspended',
            'can_publish' => false,
        ]);

        return back()->with('success', "Tipster {$tipster->display_name} suspended from publishing.");
    }

    public function resetAvatar(TipsterProfile $tipster): RedirectResponse
    {
        if ($tipster->avatar_path && \Illuminate\Support\Facades\File::exists(public_path($tipster->avatar_path))) {
            \Illuminate\Support\Facades\File::delete(public_path($tipster->avatar_path));
        }

        $tipster->update([
            'avatar_url' => null,
            'avatar_path' => null,
            'avatar_mime' => null,
            'avatar_uploaded_at' => null,
        ]);

        return back()->with('success', "Profile picture for {$tipster->display_name} has been reset to default initials.");
    }

    public function destroy(TipsterProfile $tipster): RedirectResponse
    {
        $user = $tipster->user;
        $tipster->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->route('admin.tipsters.index')->with('success', 'Tipster and associated account removed.');
    }
}
