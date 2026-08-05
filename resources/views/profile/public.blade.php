@extends('layouts.app')

@section('content')
    <div class="w-[95%] max-w-350 mx-auto pt-32 pb-8">

        {{-- ================================================================
         HEADER — cover, avatar, name, bio, stats
         ================================================================ --}}
        <div class="mb-6 pb-0">
            <div class="h-64 sm:h-80 w-full relative rounded-3xl overflow-hidden shadow-sm">
                @php
                    $cover = $user->cover_url;
                    $coverSrc = $cover
                        ? (str_contains($cover, 'http') ? $cover : asset('storage/' . $cover))
                        : asset('images/profile/banner.jpeg');
                @endphp
                <img src="{{ $coverSrc }}" alt="{{ __('profile.cover_alt') }}" class="w-full h-full object-cover">

                <div class="absolute inset-x-0 bottom-0 h-1/2 bg-linear-to-t from-black/70 to-transparent"></div>

                <div class="absolute bottom-4 sm:bottom-6 left-4 md:left-56 right-4 sm:right-auto flex justify-start z-10">
                    <div class="flex items-center gap-2 md:gap-4 pl-32 md:pl-0 pr-2">
                        <h1
                            class="text-2xl sm:text-3xl md:text-4xl font-bold text-white tracking-wide drop-shadow-md leading-tight line-clamp-2">
                            {{ $user->username }}</h1>

                        @if ($user->email_verified_at)
                            <img src="{{ asset('images/profile/verified.png') }}" alt="{{ __('profile.verified_alt') }}"
                                class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 drop-shadow-sm shrink-0">
                        @endif
                    </div>
                </div>
            </div>

            <div class="relative px-4 sm:px-8 pt-4 pb-6">
                <div class="absolute -top-16 md:-top-20 left-4 md:left-8 z-20">
                    <div
                        class="w-28 h-28 md:w-44 md:h-44 rounded-full border-4 md:border-[6px] border-[#E7E7E7] overflow-hidden bg-gray-200">
                        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->username }}"
                            class="w-full h-full object-cover">
                    </div>
                </div>

                <div class="ml-0 md:ml-50 mt-16 md:mt-2">
                    <div class="flex gap-8 text-sm mt-2">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/profile/post.png') }}" alt="{{ __('profile.post_icon_alt') }}"
                                class="w-6 h-6 object-contain">
                            <div class="flex flex-col">
                                <span
                                    class="font-extrabold text-[#094174] text-base leading-none">{{ $user->posts()->count() }}</span>
                                <span
                                    class="text-[10px] font-bold text-[#6D8A9F] tracking-wide uppercase mt-0.5">{{ __('profile.climber_posts') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/profile/join.png') }}" alt="{{ __('profile.join_icon_alt') }}"
                                class="w-6 h-6 object-contain">
                            <div class="flex flex-col">
                                <span
                                    class="font-extrabold text-[#094174] text-base leading-none">{{ $user->created_at ? $user->created_at->translatedFormat('M Y') : __('profile.not_available') }}</span>
                                <span
                                    class="text-[10px] font-bold text-[#6D8A9F] tracking-wide uppercase mt-0.5">{{ __('profile.joined_date') }}</span>
                            </div>
                        </div>
                    </div>

                    <p class="mt-5 text-[#334155] text-[13px] max-w-2xl leading-relaxed">
                        {{ $user->bio ?: __('profile.public_no_bio') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-8">

            <div class="space-y-8">
                {{-- Recent forum posts --}}
                <section>
                    <h2 class="text-xl md:text-2xl font-bold text-[#0F172A] mb-4">{{ __('profile.public_forum_posts') }}</h2>
                    <div class="space-y-4">
                        @forelse ($forumPosts as $post)
                            <a href="{{ route('community.posts.show', $post->id) }}"
                                class="block bg-white rounded-3xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] hover:shadow-lg transition-shadow">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($post->tags as $tag)
                                            <span
                                                class="bg-blue-100 text-blue-500 text-[10px] md:text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap">{{ $tag->keyword }}</span>
                                        @endforeach
                                    </div>
                                    <span class="text-xs text-gray-400 whitespace-nowrap mt-1">
                                        {{ $post->created_at->translatedFormat('M d, Y') }}
                                    </span>
                                </div>
                                @if ($post->body)
                                    <p class="text-[#1a2b4c] text-sm md:text-base">{{ Str::limit($post->body, 300) }}</p>
                                @endif
                            </a>
                        @empty
                            <div class="bg-white rounded-3xl p-6 shadow-sm text-center py-10 text-gray-500">
                                {{ __('profile.public_no_forum_posts') }}
                            </div>
                        @endforelse
                    </div>
                </section>

                {{-- Recent summit logs --}}
                <section>
                    <h2 class="text-xl md:text-2xl font-bold text-[#0F172A] mb-4">{{ __('profile.public_summit_logs') }}</h2>
                    <div class="space-y-4">
                        @forelse ($summitLogs as $log)
                            <div class="bg-white rounded-3xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)]">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="font-bold text-[#0F172A] text-sm md:text-base">
                                        {{ $log->title ?: __('profile.public_untitled_log') }}</h3>
                                    <span class="text-xs text-gray-400 whitespace-nowrap mt-0.5">
                                        {{ $log->created_at->translatedFormat('M d, Y') }}
                                    </span>
                                </div>
                                @if ($log->mountain)
                                    <p class="text-[11px] font-bold text-[#2A5C9A] uppercase tracking-wide mt-1">
                                        {{ $log->mountain->name }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="bg-white rounded-3xl p-6 shadow-sm text-center py-10 text-gray-500">
                                {{ __('profile.public_no_summit_logs') }}
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="space-y-8">
                {{-- Achievements earned --}}
                <div class="bg-white rounded-3xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)]">
                    <h3 class="font-bold text-[#0F172A] text-lg mb-5">{{ __('profile.public_achievements') }}</h3>
                    <div class="space-y-4">
                        @forelse ($achievements as $achievement)
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-xl shrink-0">
                                    {{ $achievement->icon_url ?? '🎖️' }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-[13px] font-bold text-[#334155]">{{ $achievement->title }}</h4>
                                    <p class="text-[11px] text-gray-500 leading-relaxed">
                                        {{ $achievement->localizedDescription() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">{{ __('profile.public_no_achievements') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- Recent mountain reviews --}}
                <div class="bg-white rounded-3xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)]">
                    <h3 class="font-bold text-[#0F172A] text-lg mb-5">{{ __('profile.public_reviews') }}</h3>
                    <div class="flex flex-col gap-5">
                        @forelse ($reviews as $review)
                            @continue(! $review->mountain)
                            <a href="{{ route('explore.show', $review->mountain->id) }}"
                                class="flex items-center justify-between group">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img src="{{ $review->mountain->images->first()?->image_url ?? asset('images/default-mountain.jpg') }}"
                                        alt="{{ $review->mountain->name }}"
                                        class="w-11 h-11 rounded-xl object-cover group-hover:ring-2 ring-[#094174] transition-all">
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-bold text-[#0F172A] group-hover:text-[#094174] transition">
                                            {{ Str::limit($review->mountain->name, 20) }}</h4>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">
                                            {{ $review->created_at->translatedFormat('M d, Y') }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-yellow-500 flex items-center gap-1 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-3 h-3">
                                        <path fill-rule="evenodd"
                                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ number_format($review->score, 1) }}
                                </span>
                            </a>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">{{ __('profile.public_no_reviews') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
