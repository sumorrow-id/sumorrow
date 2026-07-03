@props(['community', 'joined' => false])

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full overflow-hidden hover:shadow-md transition">
    <!-- Card Header with Image -->
    <div class="relative h-40 bg-gradient-to-br from-[#094174] to-[#105DA3] overflow-hidden">
        @if ($community->image_url)
            <img src="{{ $community->image_url }}" alt="{{ $community->name }}"
                onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'"
                class="w-full h-full object-cover" />
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-white/30" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
            </div>
        @endif

        <!-- Privacy Badge -->
        <div class="absolute top-3 left-3">
            <span
                class="inline-block px-3 py-1 text-xs font-bold text-white rounded-full {{ $community->privacy === 'public' ? 'bg-green-500/80' : 'bg-purple-500/80' }}">
                {{ __('community.privacy_'.$community->privacy) }}
            </span>
        </div>
    </div>

    <!-- Card Body -->
    <div class="p-4 flex flex-col flex-grow">
        <h3 class="font-bold text-lg text-[#1a2b4c] mb-1 line-clamp-2">
            {{ $community->name }}
        </h3>

        @if ($community->description)
            <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                {{ $community->description }}
            </p>
        @endif

        <!-- Member Count -->
        <div class="flex items-center gap-1 text-sm text-gray-500 mb-4 mt-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M12 12a3 3 0 100-6 3 3 0 000 6zm0 1.5c-4.42 0-8 2.686-8 6 0 .55.45 1 1 1h14c.55 0 1-.45 1-1 0-3.314-3.58-6-8-6z" />
            </svg>
            <span class="font-medium">{{ __('community.members_count', ['count' => $community->getMemberCount()]) }}</span>
        </div>

        <!-- Action Button -->
        @if ($joined)
            <form method="POST" action="{{ route('community.leave', $community) }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full py-2 px-4 bg-red-500/10 text-red-600 font-bold text-sm rounded-lg hover:bg-red-500/20 transition border border-red-200">
                    {{ __('community.leave_community_button') }}
                </button>
            </form>
        @else
            <form method="POST" action="{{ route('community.join', $community) }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full py-2 px-4 bg-[#094174] text-white font-bold text-sm rounded-lg hover:bg-[#105DA3] transition shadow-md">
                    {{ __('community.join_community_button') }}
                </button>
            </form>
        @endif
    </div>
</div>
