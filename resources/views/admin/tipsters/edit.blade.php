@extends('layouts.admin')

@section('title', 'Edit Tipster: ' . $tipster->display_name)
@section('page_title', 'Configure Tipster Profile')
@section('page_subtitle', 'Update badges, win rates, publishing rights and administrative verification')

@section('content')
<div class="max-w-2xl bg-forest-card border border-forest-border rounded-2xl p-6 space-y-6">
    <div class="flex items-center gap-4 pb-4 border-b border-forest-border">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center font-black text-white text-xl" style="background-color: {{ $tipster->avatar_bg }}">
            {{ substr($tipster->display_name, 0, 1) }}
        </div>
        <div>
            <h3 class="text-lg font-black text-white">{{ $tipster->display_name }} ({{ $tipster->handle }})</h3>
            <p class="text-xs text-gray-400">Account: {{ $tipster->user->email }} &bull; {{ $tipster->user->phone }}</p>
        </div>
    </div>

    <form action="{{ route('admin.tipsters.update', $tipster) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Display Name</label>
                <input type="text" name="display_name" value="{{ old('display_name', $tipster->display_name) }}" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Specialty Description</label>
                <input type="text" name="specialty" value="{{ old('specialty', $tipster->specialty) }}" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Win Rate (%)</label>
                <input type="number" step="0.1" name="win_rate" value="{{ old('win_rate', $tipster->win_rate) }}" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Badge Tag</label>
                <input type="text" name="badge" value="{{ old('badge', $tipster->badge) }}" class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white" placeholder="TOP TIPSTER 🔥">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Current Streak</label>
                <input type="text" name="streak" value="{{ old('streak', $tipster->streak) }}" required class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white" placeholder="7W Streak">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Account Status</label>
                <select name="status" class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2.5 text-sm text-white">
                    <option value="pending" {{ $tipster->status === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="approved" {{ $tipster->status === 'approved' ? 'selected' : '' }}>Approved & Active</option>
                    <option value="suspended" {{ $tipster->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div class="flex items-center gap-6 pt-5">
                <label class="flex items-center gap-2 cursor-pointer text-xs font-bold">
                    <input type="checkbox" name="is_verified" value="1" {{ $tipster->is_verified ? 'checked' : '' }} class="rounded bg-forest-dark border-forest-border text-neon">
                    <span>Verified Badge (✓)</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer text-xs font-bold">
                    <input type="checkbox" name="can_publish" value="1" {{ $tipster->can_publish ? 'checked' : '' }} class="rounded bg-forest-dark border-forest-border text-neon">
                    <span>Can Publish Slips</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-300 uppercase mb-1">Super Admin Notes</label>
            <textarea name="approval_notes" rows="3" class="w-full bg-forest-dark border border-forest-border rounded-xl px-4 py-2 text-sm text-white" placeholder="Notes on analysis accuracy, license or history...">{{ old('approval_notes', $tipster->approval_notes) }}</textarea>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-forest-border">
            <a href="{{ route('admin.tipsters.index') }}" class="text-xs text-gray-400 hover:text-white">&larr; Back to Tipsters</a>
            <button type="submit" class="px-5 py-2.5 bg-neon hover:bg-neon/90 text-forest-dark font-black text-xs rounded-xl transition shadow">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
