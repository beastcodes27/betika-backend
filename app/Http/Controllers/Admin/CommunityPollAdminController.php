<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityPoll;
use Illuminate\Http\Request;

class CommunityPollAdminController extends Controller
{
    public function index()
    {
        $polls = CommunityPoll::latest()->paginate(15);
        return view('admin.polls.index', compact('polls'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'match_title' => 'required|string|max:150',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:100',
            'expires_at' => 'nullable|date',
        ]);

        $optionsData = [];
        foreach ($validated['options'] as $optionText) {
            $optionsData[] = [
                'text' => $optionText,
                'votes' => 0,
            ];
        }

        CommunityPoll::create([
            'question' => $validated['question'],
            'match_title' => $validated['match_title'],
            'options' => $optionsData,
            'total_votes' => 0,
            'is_active' => true,
            'expires_at' => $validated['expires_at'] ?? now()->addDays(3),
        ]);

        return back()->with('success', 'New community match prediction poll launched successfully!');
    }

    public function toggleActive(CommunityPoll $poll)
    {
        $poll->update(['is_active' => !$poll->is_active]);
        $status = $poll->is_active ? 'Opened' : 'Closed';
        return back()->with('success', "Poll #{$poll->id} is now {$status}.");
    }

    public function destroy(CommunityPoll $poll)
    {
        $poll->delete();
        return back()->with('success', "Poll #{$poll->id} deleted.");
    }
}
