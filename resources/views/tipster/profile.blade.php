@extends('layouts.admin')

@section('title', 'Tipster Profile & Avatar Settings')
@section('header', 'Profile Picture & Tipster Branding')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Current Avatar & Quick Actions -->
        <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-6 shadow-lg flex flex-col items-center text-center">
            <div class="relative group">
                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-[#DDD06A] shadow-xl bg-[#013220] flex items-center justify-center">
                    @if($tipster->avatar_url)
                        <img id="avatar-preview-img" src="{{ $tipster->avatar_url }}" alt="{{ $tipster->display_name }}" class="w-full h-full object-cover">
                    @else
                        <div id="avatar-fallback" class="w-full h-full flex items-center justify-center text-4xl font-extrabold text-[#DDD06A]">
                            {{ substr($tipster->display_name, 0, 1) }}
                        </div>
                    @endif
                </div>

                <div class="absolute bottom-1 right-1 bg-[#39FF88] text-[#013220] p-1.5 rounded-full shadow border-2 border-[#0b291a]">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>

            <h3 class="text-lg font-bold text-white mt-4">{{ $tipster->display_name }}</h3>
            <span class="text-xs font-mono text-[#DDD06A]">{{ $tipster->handle }}</span>

            <div class="mt-3 flex items-center space-x-2">
                <span class="text-xs bg-[#013220] border border-[#1b4332] text-[#39FF88] px-2.5 py-1 rounded-full font-bold">
                    {{ $tipster->badge ?? 'VERIFIED TIPSTER' }}
                </span>
            </div>

            <div class="mt-4 pt-4 border-t border-[#1b4332] w-full text-xs text-gray-400 space-y-1">
                <div>Win Rate: <strong class="text-white">{{ $tipster->win_rate }}%</strong></div>
                <div>Followers: <strong class="text-white">{{ number_format($tipster->followers_count) }}</strong></div>
                @if($tipster->avatar_uploaded_at)
                    <div class="text-[11px] text-gray-500 pt-1">Photo updated {{ \Carbon\Carbon::parse($tipster->avatar_uploaded_at)->diffForHumans() }}</div>
                @endif
            </div>

            @if($tipster->avatar_url)
                <form method="POST" action="{{ route('tipster.profile.remove-avatar') }}" class="mt-4 w-full">
                    @csrf
                    <button type="submit" class="w-full text-xs text-red-400 hover:text-red-300 py-1.5 border border-red-900/50 rounded-lg hover:bg-red-950/30 transition">
                        Remove Profile Picture
                    </button>
                </form>
            @endif
        </div>

        <!-- Avatar Upload & Profile Details -->
        <div class="md:col-span-2 space-y-6">
            <!-- Upload Box -->
            <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-6 shadow-lg">
                <h3 class="text-base font-bold text-[#DDD06A] mb-2">Upload New Profile Picture</h3>
                <p class="text-xs text-gray-300 mb-4">
                    Your profile picture is displayed on all published betslips, community feeds, and author cards across the mobile app.
                </p>

                <form method="POST" action="{{ route('tipster.profile.upload-avatar') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="border-2 border-dashed border-[#1b4332] hover:border-[#DDD06A] rounded-xl p-6 text-center cursor-pointer transition bg-[#013220]/50" onclick="document.getElementById('avatar-input').click()">
                        <svg class="mx-auto h-12 w-12 text-[#DDD06A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-200">
                            <span class="font-semibold text-[#DDD06A]">Click to choose file</span> or drag and drop photo
                        </p>
                        <p class="text-xs text-gray-400 mt-1">PNG, JPG, or WEBP up to 3MB (Square 1:1 recommended)</p>
                        <input id="avatar-input" type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="hidden" onchange="previewImage(this)">
                    </div>

                    <div id="file-chosen-name" class="hidden text-xs text-emerald-400 font-mono text-center"></div>

                    <button type="submit" class="w-full bg-[#DDD06A] hover:bg-yellow-400 text-[#013220] font-bold py-2.5 rounded-lg text-sm transition">
                        Upload & Update Avatar
                    </button>
                </form>
            </div>

            <!-- Profile Details Form -->
            <div class="bg-[#0b291a] border border-[#1b4332] rounded-xl p-6 shadow-lg">
                <h3 class="text-base font-bold text-white mb-4">Tipster Details</h3>
                <form method="POST" action="{{ route('tipster.profile.update') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Display Name</label>
                        <input type="text" name="display_name" value="{{ old('display_name', $tipster->display_name) }}" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Specialty & Markets</label>
                        <input type="text" name="specialty" value="{{ old('specialty', $tipster->specialty) }}" placeholder="e.g. European Leagues, Goals 2.5+, High-Odds Doubles" required class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Bio / Analytical Philosophy</label>
                        <textarea name="bio" rows="3" placeholder="Describe your statistical strategy and match selection method..." class="w-full bg-[#013220] border border-[#1b4332] rounded-lg px-3 py-2 text-sm text-white">{{ old('bio', $tipster->bio) }}</textarea>
                    </div>

                    <button type="submit" class="bg-emerald-700 hover:bg-emerald-600 text-white font-bold px-6 py-2 rounded-lg text-sm transition">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        document.getElementById('file-chosen-name').textContent = 'Selected: ' + file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
        document.getElementById('file-chosen-name').classList.remove('hidden');

        const reader = new FileReader();
        reader.onload = function(e) {
            let img = document.getElementById('avatar-preview-img');
            if (!img) {
                const fallback = document.getElementById('avatar-fallback');
                if (fallback) fallback.remove();
                img = document.createElement('img');
                img.id = 'avatar-preview-img';
                img.className = 'w-full h-full object-cover';
                document.querySelector('.w-32.h-32').appendChild(img);
            }
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
