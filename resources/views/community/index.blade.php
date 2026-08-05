@extends('layouts.app')

@section('content')
<div class="w-[95%] sm:w-[90%] max-w-7xl mx-auto pt-24 md:pt-32 pb-8">

    <div class="relative rounded-2xl md:rounded-3xl overflow-hidden mb-6 md:mb-8 shadow-md">

        <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2670&auto=format&fit=crop"
             alt="{{ __('community.hero_image_alt') }}"
             class="absolute inset-0 w-full h-full object-cover z-0" />

        <div class="absolute inset-0 bg-[#03305c]/85 z-10"></div>

        <div class="relative z-20 px-6 pt-20 pb-6 md:px-12 md:pt-32 md:pb-8">
            <span class="text-white/90 text-xs md:text-sm font-bold tracking-widest uppercase mb-3 md:mb-4 block">
                {{ __('community.forum_discussion_label') }}
            </span>
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-3 md:mb-4 tracking-tight leading-tight max-w-3xl">
                {{ __('community.hero_title_line1') }}<br class="hidden sm:block">{{ __('community.hero_title_line2') }}
            </h1>
            <p class="text-white/90 text-xs sm:text-sm md:text-base leading-relaxed max-w-3xl">
                {{ __('community.hero_description') }}
            </p>
        </div>
    </div>

    <div class="flex flex-col gap-6">

        <div class="flex items-center border-b border-gray-200 px-2 overflow-x-auto justify-between hide-scrollbar" id="forum-tabs">
            <div class="flex items-center gap-4 sm:gap-6">
                <button
                    data-forum-tab="explore"
                    id="tab-explore"
                    class="pb-3 border-b-2 transition-all duration-300 text-sm sm:text-base cursor-pointer border-[#094174] text-[#094174] font-bold whitespace-nowrap">
                    {{ __('community.tab_explore') }}
                </button>
                <button
                    data-forum-tab="community"
                    id="tab-community"
                    class="pb-3 border-b-2 transition-all duration-300 text-sm sm:text-base cursor-pointer border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap">
                    {{ __('community.my_community') }}
                </button>
            </div>

            <button
                class="mobile-sidebar-toggle pb-3 text-sm sm:text-base cursor-pointer text-gray-500 hover:text-[#094174] transition whitespace-nowrap flex items-center gap-1.5 lg:hidden">
                <span class="font-bold">{{ __('community.discover') }}</span>
            </button>
        </div>

        <div class="py-4 md:py-6">
            <div id="content-explore" class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
                <div class="col-span-1 lg:col-span-2">
                    @include('community.components.feed')
                </div>
                
                <div class="hidden lg:block lg:col-span-1 border-gray-100 lg:pl-4">
                    @include('community.components.sidebar')
                </div>
            </div>

            <div id="content-community" class="hidden grid-cols-1 gap-6 md:gap-8">
                
                {{-- Top Bar: Title & Create Community Button --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-heading">{{ __('community.my_communities_heading') }}</h2>
                        <p class="text-gray-500 text-sm mt-1">{{ __('community.my_communities_subheading') }}</p>
                    </div>

                    @auth
                        <button class="open-create-community-modal bg-[#094174] text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-[#105DA3] transition-all shadow-md flex items-center gap-2 cursor-pointer self-start sm:self-auto shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('community.create_community') }}
                        </button>
                    @endauth

                    @guest
                        <a href="{{ route('showLogin') }}" class="bg-[#094174] text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-[#105DA3] transition-all shadow-md items-center gap-2 inline-flex decoration-none self-start sm:self-auto shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('community.create_community') }}
                        </a>
                    @endguest
                </div>

                {{-- Search: filters both the joined list and the suggestions below --}}
                <form method="GET" action="{{ route('community.explore') }}" role="search">
                    <input type="hidden" name="tab" value="community">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="community_search"
                            value="{{ $communitySearch }}"
                            placeholder="{{ __('community.search_communities_placeholder') }}"
                            class="block w-full pl-11 pr-4 py-3 bg-[#F8F9FA] rounded-xl text-sm md:text-base border-none focus:ring-0 focus:outline-none placeholder-gray-400">
                    </div>
                </form>

                {{-- Main: Joined Communities Grid --}}
                @if ($myCommunities->isEmpty())
                    <div class="text-center py-12 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM15 20H9m6 0h6" />
                            </svg>
                        </div>
                        @if ($communitySearch)
                            <h3 class="text-lg font-bold text-[#001E3A] mb-1">{{ __('community.no_communities_found', ['search' => $communitySearch]) }}</h3>
                            <a href="{{ route('community.explore', ['tab' => 'community']) }}" class="text-sm font-bold text-[#094174] hover:underline">{{ __('community.clear_filter_aria') }}</a>
                        @else
                            <h3 class="text-lg font-bold text-[#001E3A] mb-1">{{ __('community.no_communities_joined') }}</h3>
                            <p class="text-gray-400 text-sm mb-4">{{ __('community.discover_recommendations_text') }}</p>
                        @endif
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($myCommunities as $community)
                            <x-community-card :community="$community" :joined="true" />
                        @endforeach
                    </div>

                    {{ $myCommunities->links() }}
                @endif

                {{-- Section: Suggested For You --}}
                <div class="mt-4 border-t border-gray-100 pt-8">
                    <h3 class="text-xl font-bold text-[#001E3A] mb-4">{{ __('community.suggested_for_you') }}</h3>

                    @if ($suggestedCommunities->isEmpty())
                        <p class="text-sm text-gray-400">
                            {{ $communitySearch ? __('community.no_communities_found', ['search' => $communitySearch]) : __('community.no_recommendations') }}
                        </p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($suggestedCommunities as $suggested)
                                <x-community-card :community="$suggested" :joined="false" />
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $suggestedCommunities->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 3. Modal: Create Community (Pop-up Form) --}}
<div id="modal-create-community" class="hidden fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4 backdrop-blur-xs">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl sm:text-2xl font-bold text-[#001E3A]">{{ __('community.create_community') }}</h3>
            <button type="button" class="close-create-community-modal text-gray-400 hover:text-gray-600 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('community.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-[#1a2b4c] mb-1">{{ __('community.community_name_label') }}</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#094174] text-sm text-heading" placeholder="{{ __('community.community_name_placeholder') }}" />
            </div>

            <div>
                <label class="block text-sm font-bold text-[#1a2b4c] mb-1">{{ __('community.description_label') }}</label>
                <textarea name="description" rows="3" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#094174] text-sm text-heading resize-none" placeholder="{{ __('community.community_description_placeholder') }}"></textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-[#1a2b4c] mb-1">{{ __('community.privacy_label') }}</label>
                <select name="privacy" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#094174] text-sm text-heading bg-white">
                    <option value="public">{{ __('community.privacy_public_option') }}</option>
                    <option value="private">{{ __('community.privacy_private_option') }}</option>
                </select>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="button" class="close-create-community-modal flex-1 px-4 py-2 border border-gray-200 text-gray-500 font-bold rounded-full hover:bg-gray-50 transition cursor-pointer">
                    {{ __('common.cancel') }}
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-[#094174] text-white font-bold rounded-full hover:bg-[#105DA3] transition shadow-md cursor-pointer">
                    {{ __('community.create_button') }}
                </button>
            </div>
        </form>
    </div>
</div>


<div id="mobile-sidebar-overlay" class="mobile-sidebar-toggle fixed inset-0 bg-black/50 z-100 hidden backdrop-blur-sm transition-opacity opacity-0"></div>
    <div id="mobile-sidebar-drawer" class="fixed top-0 right-0 h-full w-[85%] sm:w-[60%] max-w-sm bg-gray-50 z-110 transform translate-x-full transition-transform duration-300 overflow-y-auto lg:hidden">
        <div class="p-5 flex items-center justify-between border-b border-gray-100 bg-white sticky top-0 z-10">
            <h2 class="font-bold text-lg text-[#1a2b4c]">{{ __('community.discover') }}</h2>
            <button class="mobile-sidebar-toggle text-gray-400 hover:text-gray-600 transition p-1 bg-gray-100 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-5">
            @include('community.components.sidebar')
        </div>
    </div>
</div>

@endsection
