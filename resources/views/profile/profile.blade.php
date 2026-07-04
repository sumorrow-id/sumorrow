@extends('layouts.app')

@section('content')
    <div class="w-[95%] max-w-350 mx-auto pt-32 pb-8">

        <div class="mb-6 pb-0">
            <div class="h-64 sm:h-80 w-full relative rounded-3xl overflow-hidden shadow-sm">
                @php
                    $cover = Auth::user()->cover_url;
                    $coverSrc = $cover ? (str_contains($cover, 'http') ? $cover : asset('storage/' . $cover)) : asset('images/profile/banner.jpeg');
                @endphp
                <img src="{{ $coverSrc }}" alt="{{ __('profile.cover_alt') }}" class="w-full h-full object-cover">

                <div class="absolute inset-x-0 bottom-0 h-1/2 bg-linear-to-t from-black/70 to-transparent"></div>

                <div
                    class="absolute bottom-4 sm:bottom-6 left-4 md:left-56 right-4 sm:right-auto flex justify-start z-10 overflow-visible">
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-3 md:gap-4 pl-32 md:pl-0 pr-2">
                        <div class="flex items-center gap-2 md:gap-4 w-full">
                            <h1
                                class="text-2xl sm:text-3xl md:text-4xl font-bold text-white tracking-wide drop-shadow-md leading-tight line-clamp-2">
                                {{ Auth::user()->username }}</h1>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="px-4 py-2 md:px-5 md:py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white rounded-full text-xs md:text-sm font-semibold flex items-center gap-2 transition shadow-sm border border-white/30 md:ml-2 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="h-3 w-3 fill-current">
                                <path d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-45.3 90.5L32 394.7V480H117.3L402.3 195 272.3 65 224.2 113.1z"/>
                            </svg>
                            {{ __('profile.edit_profile') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="relative px-4 sm:px-8 pt-4 pb-6">
                <div class="absolute -top-16 md:-top-20 left-4 md:left-8 z-20">
                    <div
                        class="w-28 h-28 md:w-44 md:h-44 rounded-full border-4 md:border-[6px] border-[#E7E7E7] overflow-hidden bg-gray-200">
                        @php
                            $avatar = Auth::user()->avatar_url;
                            $src = $avatar ? (str_contains($avatar, 'http') ? $avatar : asset('storage/' . $avatar)) : null;
                            $defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode(substr(Auth::user()->username, 0, 2)) . '&background=random';
                        @endphp
                        <img src="{{ $avatar ? $src : $defaultAvatar }}" alt="{{ Auth::user()->username }}"
                            class="w-full h-full object-cover">
                    </div>
                </div>

                <div class="ml-0 md:ml-50 mt-16 md:mt-2">
                    <div class="flex gap-8 text-sm mt-2">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/profile/post.png') }}" alt="{{ __('profile.post_icon_alt') }}"
                                class="w-6 h-6 object-contain">
                            <div class="flex flex-col">
                                <span class="font-extrabold text-[#094174] text-base leading-none">{{ Auth::user()->posts()->count() }}</span>
                                <span class="text-[10px] font-bold text-[#6D8A9F] tracking-wide uppercase mt-0.5">{{ __('profile.climber_posts') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/profile/join.png') }}" alt="{{ __('profile.join_icon_alt') }}"
                                class="w-6 h-6 object-contain">
                            <div class="flex flex-col">
                                <span class="font-extrabold text-[#094174] text-base leading-none">{{ Auth::user()->created_at ? Auth::user()->created_at->translatedFormat('M Y') : __('profile.not_available') }}</span>
                                <span class="text-[10px] font-bold text-[#6D8A9F] tracking-wide uppercase mt-0.5">{{ __('profile.joined_date') }}</span>
                            </div>
                        </div>
                    </div>

                    <p class="mt-5 text-[#334155] text-[13px] max-w-2xl leading-relaxed">
                        {{ Auth::user()->bio ?? __('profile.default_bio') }}
                    </p>
                </div>

                <div class="border-b-[1.5px] border-gray-300 mt-8 md:mt-10 -mx-4 sm:-mx-8 px-4 sm:px-8 overflow-x-auto whitespace-nowrap hide-scrollbar"
                    id="tab-navigation">
                    <div class="flex gap-6 md:gap-10 w-max">
                        <button data-tab="posts" id="btn-posts"
                            class="tab-btn border-b-[3px] border-[#094174] pb-3 pt-1 text-[14px] md:text-[15px] font-bold text-[#094174] hover:text-gray-700 translate-y-[1.5px]">{{ __('profile.tab_posts') }}</button>
                        <button data-tab="hikings" id="btn-hikings"
                            class="tab-btn border-b-[3px] border-transparent pb-3 pt-1 text-[14px] md:text-[15px] font-semibold text-gray-400 hover:text-gray-700 translate-y-[1.5px]">{{ __('profile.tab_hikings') }}</button>
                        <button data-tab="achievements" id="btn-achievements"
                            class="tab-btn border-b-[3px] border-transparent pb-3 pt-1 text-[14px] md:text-[15px] font-semibold text-gray-400 hover:text-gray-700 translate-y-[1.5px]">{{ __('profile.tab_achievements') }}</button>
                        <button data-tab="gear" id="btn-gear"
                            class="tab-btn border-b-[3px] border-transparent pb-3 pt-1 text-[14px] md:text-[15px] font-semibold text-gray-400 hover:text-gray-700 translate-y-[1.5px]">{{ __('profile.tab_gear') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="tab-contents">
            <div id="content-posts" class="tab-content block">
                @include('profile.partials.posts')
            </div>

            <div id="content-hikings" class="tab-content hidden">
                @include('profile.partials.hikings')
            </div>

            <div id="content-achievements" class="tab-content hidden">
                @include('profile.partials.achievements')
            </div>

            <div id="content-gear" class="tab-content hidden">
                @include('profile.partials.gear')
            </div>
        </div>
    </div>
@endsection
