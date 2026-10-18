<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bookmaker;
use Illuminate\Http\Request;

class BookmakerAdminController extends Controller
{
    public function index()
    {
        $bookmakers = Bookmaker::withCount('betslips')->orderBy('id')->get();
        return view('admin.bookmakers.index', compact('bookmakers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:bookmakers,slug',
            'logo_url' => 'nullable|string',
            'primary_color' => 'nullable|string|max:20',
            'website_url' => 'nullable|url',
            'affiliate_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        Bookmaker::create(array_merge($validated, [
            'is_active' => true,
        ]));

        return back()->with('success', "Bookmaker '{$validated['name']}' registered.");
    }

    public function toggleActive(Bookmaker $bookmaker)
    {
        $bookmaker->update(['is_active' => !$bookmaker->is_active]);
        $status = $bookmaker->is_active ? 'Enabled' : 'Disabled';
        return back()->with('success', "Bookmaker '{$bookmaker->name}' is now {$status}.");
    }

    public function update(Request $request, Bookmaker $bookmaker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'logo_url' => 'nullable|string',
            'primary_color' => 'nullable|string|max:20',
            'website_url' => 'nullable|url',
            'affiliate_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $bookmaker->update($validated);

        return back()->with('success', "Bookmaker '{$bookmaker->name}' updated.");
    }
}
