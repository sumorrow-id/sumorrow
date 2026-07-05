@props(['community', 'joined' => false])

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full overflow-hidden hover:shadow-md transition">
    <!-- Card Header with Image -->
    <a href="{{ route('community.show', $community) }}" class="relative block h-40 bg-gray-100 overflow-hidden"
        aria-label="{{ __('community.view_community') }}">
        <img src="{{ $community->bannerImageUrl() }}" alt="{{ $community->name }}"
            onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'"
            class="w-full h-full object-cover" />

        <!-- Privacy / Owner Badges -->
        <div class="absolute top-3 left-3 flex items-center gap-2">
            <span
                class="inline-block px-3 py-1 text-xs font-bold text-white rounded-full {{ $community->privacy === 'public' ? 'bg-green-500/80' : 'bg-purple-500/80' }}">
                {{ __('community.privacy_'.$community->privacy) }}
            </span>
            @if ($community->isCreatedBy(Auth::user()))
                <span class="inline-block px-3 py-1 text-xs font-bold text-white rounded-full bg-[#094174]/90">
                    {{ __('community.owner_badge') }}
                </span>
            @endif
        </div>
    </a>

    <!-- Card Body -->
    <div class="p-4 flex flex-col flex-grow">
        <a href="{{ route('community.show', $community) }}">
            <h3 class="font-bold text-lg text-[#1a2b4c] mb-1 line-clamp-2 hover:text-[#094174] transition">
                {{ $community->name }}
            </h3>
        </a>

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

        <!-- Action Buttons -->
        <div class="flex items-center gap-2">
            <a href="{{ route('community.show', $community) }}"
                class="flex-1 py-2 px-4 text-center bg-gray-100 text-[#094174] font-bold text-sm rounded-lg hover:bg-gray-200 transition">
                {{ __('community.view_community') }}
            </a>

            @if ($joined)
                <form method="POST" action="{{ route('community.leave', $community) }}" class="confirm-submit-form flex-1"
                    data-confirm-title="{{ __('community.leave_community_button') }}"
                    data-confirm-message="{{ __('community.confirm_leave_community') }}"
                    data-confirm-label="{{ __('community.confirm_leave_button') }}"
                    data-confirm-variant="danger">
                    @csrf
                    <button type="submit"
                        class="w-full py-2 px-4 bg-red-500/10 text-red-600 font-bold text-sm rounded-lg hover:bg-red-500/20 transition border border-red-200 cursor-pointer">
                        {{ __('community.leave_community_button') }}
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('community.join', $community) }}" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full py-2 px-4 bg-[#094174] text-white font-bold text-sm rounded-lg hover:bg-[#105DA3] transition shadow-md cursor-pointer">
                        {{ __('community.join_community_button') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
