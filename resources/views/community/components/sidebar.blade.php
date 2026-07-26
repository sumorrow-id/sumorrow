<div class="flex flex-col gap-6">


    {{-- Search Bar --}}
    <form method="GET" action="{{ route('community.explore') }}" role="search">
        <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="{{ __('community.search_posts_placeholder') }}"
                class="block w-full pl-11 pr-4 py-3 bg-[#F8F9FA] rounded-xl text-sm md:text-base border-none focus:ring-0 focus:outline-none placeholder-gray-400">
        </div>
    </form>

    {{-- 1. Forum Leaders --}}
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 md:p-6">
        <h2 class="font-bold text-lg text-[#1a2b4c] mb-2">{{ __('community.forum_leaders_heading') }}</h2>
        <p class="text-xs md:text-sm text-gray-500 mb-5 leading-relaxed">{{ __('community.forum_leaders_description') }}</p>

        <div class="flex flex-col gap-5">
            @foreach ($forumLeaders as $leader)
            <div class="flex items-center gap-4">
                <div class="relative shrink-0">
                    <img src="{{ $leader->avatarUrl(asset('images/community/profile-blank.jpg')) }}" alt="{{ __('community.avatar_alt') }}" class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover">
                </div>
                <div>
                    <div class="font-bold text-[#1a2b4c] text-sm md:text-base">{{ $leader->username }}</div>
                    <div class="text-[10px] md:text-xs text-gray-500 uppercase tracking-wide">{{ __('community.contributions_count', ['count' => number_format($leader->posts_count)]) }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- 2. My Community --}}
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 md:p-6">
        <h2 data-forum-tab="community" class="font-bold text-lg text-[#1a2b4c] mb-5 flex items-center justify-between cursor-pointer group hover:text-[#094174] transition">
            <span>{{ __('community.my_community') }}</span>
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </h2>

        <div class="flex flex-col gap-5">
            @forelse ($myCommunities->take(3) as $community)
            <a href="{{ route('community.show', $community) }}" class="flex items-center gap-4 group pb-1">
                <img src="{{ $community->profileImageUrl() }}" alt="{{ __('community.community_image_alt') }}" class="w-12 h-12 md:w-14 md:h-14 rounded-xl object-cover shrink-0">
                <div class="flex flex-col justify-center">
                    <span class="text-[9px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">{{ __('community.privacy_'.$community->privacy) }}</span>
                    <span class="font-bold text-[#1a2b4c] text-sm md:text-base group-hover:text-[#094174] transition">{{ $community->name }}</span>
                    <span class="text-xs text-gray-400 mt-0.5">{{ __('community.members_count', ['count' => number_format($community->members_count)]) }}</span>
                </div>
            </a>
            @empty
            <p class="text-sm text-gray-400">{{ __('community.no_communities_joined') }}</p>
            @endforelse
        </div>
    </div>

    {{-- 3. Popular Tags --}}
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 md:p-6">
        <h2 class="font-bold text-lg text-[#1a2b4c] mb-5">
            {{ __('community.popular_tags_heading') }}
        </h2>

        <div class="flex flex-col gap-2">
            @foreach ($popularTags as $tag)

            @php
                $isActive   = request('tag') === $tag->keyword;
                $filterHref = $isActive
                    ? route('community.explore')
                    : route('community.explore', ['tag' => $tag->keyword]);

                // Figma Color Palette Mapping
                $tagConfig = match(true) {
                    stripos($tag->keyword, 'Hiking') !== false   => ['bg' => 'bg-blue-100',   'text' => 'text-blue-500',   'hover' => 'group-hover:text-blue-500'],
                    stripos($tag->keyword, 'Tips') !== false     => ['bg' => 'bg-green-100',  'text' => 'text-green-500',  'hover' => 'group-hover:text-green-500'],
                    stripos($tag->keyword, 'Gear') !== false     => ['bg' => 'bg-purple-100', 'text' => 'text-purple-500', 'hover' => 'group-hover:text-purple-500'],
                    stripos($tag->keyword, 'Safety') !== false   => ['bg' => 'bg-red-100',    'text' => 'text-red-500',    'hover' => 'group-hover:text-red-500'],
                    default                                      => ['bg' => 'bg-blue-100',   'text' => 'text-blue-500',   'hover' => 'group-hover:text-blue-500'],
                };
            @endphp

            <a href="{{ $filterHref }}"
               class="tag-filter-btn flex items-center gap-4 cursor-pointer group px-2 py-2.5 rounded-xl transition
                      {{ $isActive ? 'bg-gray-100' : 'hover:bg-gray-50' }}">

                <div class="w-12 h-12 rounded-xl {{ $tagConfig['bg'] }} {{ $tagConfig['text'] }} shrink-0 flex items-center justify-center group-hover:scale-105 transition-transform">
                    @switch(true)
                        @case(stripos($tag->keyword, 'Hiking') !== false)
                            {{-- Hiker / person walking icon --}}
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13.5 5.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm-4.5 5l2-2 1.5 1.5L15 7l3 3-2 1v6h-2v-5l-1.5-1.5L11 13H9l-2 2-1.5-1.5L8 11h1z"/>
                            </svg>
                            @break
                        @case(stripos($tag->keyword, 'Tips') !== false)
                            {{-- Lightbulb icon --}}
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2a7 7 0 00-4 12.74V17a1 1 0 001 1h6a1 1 0 001-1v-2.26A7 7 0 0012 2zm-1 15v1h2v-1h-2zm1-13a5 5 0 013.54 8.54A3 3 0 0013 15h-2a3 3 0 00-2.54-2.46A5 5 0 0112 4z"/>
                            </svg>
                            @break
                        @case(stripos($tag->keyword, 'Gear') !== false)
                            {{-- Gear / settings cog icon --}}
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 15.5A3.5 3.5 0 018.5 12 3.5 3.5 0 0112 8.5a3.5 3.5 0 013.5 3.5 3.5 3.5 0 01-3.5 3.5m7.43-2.92c.04-.36.07-.73.07-1.08s-.03-.73-.07-1.08l2.32-1.82c.21-.16.27-.46.13-.7l-2.2-3.8c-.13-.25-.43-.33-.67-.25l-2.74 1.1c-.57-.44-1.18-.81-1.86-1.09l-.41-2.92A.49.49 0 0014 2h-4a.49.49 0 00-.49.42l-.41 2.92c-.68.28-1.3.65-1.86 1.09L4.5 5.33a.53.53 0 00-.67.25L1.63 9.38c-.14.24-.08.54.13.7l2.32 1.82A8.08 8.08 0 004 13c0 .35.03.72.07 1.07L1.75 15.9c-.21.16-.27.46-.13.7l2.2 3.8c.13.25.43.33.67.25l2.74-1.1c.57.44 1.18.81 1.86 1.09l.41 2.92c.06.26.29.44.55.44h4c.26 0 .49-.18.55-.44l.41-2.92c.68-.28 1.3-.65 1.86-1.09l2.74 1.1c.24.08.54 0 .67-.25l2.2-3.8c.14-.24.08-.54-.13-.7l-2.32-1.83z"/>
                            </svg>
                            @break
                        @case(stripos($tag->keyword, 'Safety') !== false)
                            {{-- Alert / warning triangle icon --}}
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L1 21h22L12 2zm0 3.45L20.1 19H3.9L12 5.45zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/>
                            </svg>
                            @break
                        @default
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16z"/>
                            </svg>
                    @endswitch
                </div>

                <div class="flex flex-col">
                    <span class="font-bold text-[#1a2b4c] text-sm md:text-base {{ $tagConfig['hover'] }} transition
                                 {{ $isActive ? $tagConfig['text'] : '' }}">
                        {{ $tag->keyword }}
                    </span>
                    <span class="text-xs text-gray-400">{{ __('community.posted_by_this_tag', ['count' => number_format($tag->post_count)]) }}</span>
                </div>

                {{-- Active indicator dot --}}
                @if ($isActive)
                <div class="ml-auto shrink-0 w-2 h-2 rounded-full {{ $tagConfig['text'] }} bg-current"></div>
                @endif
            </a>
            @endforeach
        </div>
    </div>

</div>
