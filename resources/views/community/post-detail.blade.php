@extends('layouts.app')

@section('content')
    <div class="w-[95%] sm:w-[90%] max-w-2xl mx-auto pt-32 pb-16">

        {{-- Back to Feed Link --}}
        <a href="{{ route('community.explore') }}"
           class="inline-flex items-center gap-1.5 text-gray-500 font-semibold text-sm mb-6 transition-colors duration-200 hover:text-[#094174] group">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-0.5"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            Back to Feed
        </a>

        {{-- ================================================================
         POST DETAIL CARD
         ================================================================ --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8 mb-6">

            {{-- Author Row --}}
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <img src="{{ $post->author->avatar_url ? asset($post->author->avatar_url) : asset('images/dummymountain/rinjani.png') }}"
                        class="w-11 h-11 rounded-full object-cover" alt="Avatar">
                    <div>
                        <div class="flex items-center gap-1">
                            <span class="font-bold text-[#1a2b4c] text-base">{{ $post->author->username }}</span>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ '@' . strtolower(str_replace(' ', '', $post->author->username)) }}</div>
                    </div>
                </div>
                {{-- Category Tag Badge --}}
                @if ($post->category_tag)
                    <div class="bg-[#e5f5fa] text-[#29b6f6] text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap">
                        {{ $post->category_tag }}
                    </div>
                @endif
            </div>

            {{-- Post Body --}}
            <p class="text-[#1a2b4c] text-base leading-relaxed mb-4">{{ $post->body }}</p>

            {{-- Post Images --}}
            @if ($post->images && $post->images->isNotEmpty())
                <div class="mb-4 flex flex-col gap-4">
                    @foreach ($post->images as $image)
                        <img src="{{ asset($image->image_url) }}" alt="Post image"
                            class="w-full max-h-[500px] object-cover rounded-2xl border border-gray-100">
                    @endforeach
                </div>
            @endif

            {{-- Post GIF --}}
            @if ($post->gif_url)
                <div class="mb-4">
                    <img src="{{ $post->gif_url }}" alt="GIF"
                        class="w-full max-h-[500px] object-cover rounded-2xl border border-gray-100">
                </div>
            @endif

            {{-- Timestamp --}}
            <div class="text-xs text-gray-400 mb-4 font-medium tracking-wide">
                {{ $post->created_at->diffForHumans() }}
                · {{ $post->created_at->format('g:i A · M j, Y') }}
            </div>

            <hr class="border-gray-100 mb-4">

            {{-- Stats Row --}}
            <div class="flex items-center gap-6 text-sm text-gray-500">
                <span><strong class="text-[#1a2b4c]">{{ $commentsCount }}</strong> Replies</span>
            </div>
        </div>

        {{-- ================================================================
         COMMENTS / REPLIES THREAD
         ================================================================ --}}
        @if ($comments->isEmpty())
            <div class="text-center text-gray-400 py-10">
                <p class="font-medium">No replies yet. Be the first to respond!</p>
            </div>
        @else
            <div class="flex flex-col gap-4 mb-6">
                @foreach ($comments as $comment)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <div class="flex items-center gap-3 mb-3">
                            <img src="{{ $comment->user->avatar_url ? asset($comment->user->avatar_url) : asset('images/dummymountain/rinjani.png') }}"
                                class="w-9 h-9 rounded-full object-cover" alt="Avatar">
                            <div>
                                <div class="font-bold text-[#1a2b4c] text-sm">{{ $comment->user->username }}</div>
                                <div class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <p class="text-[#1a2b4c] text-sm leading-relaxed">{{ $comment->body }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ================================================================
         COMMENT SUBMISSION FORM
         ================================================================ --}}
        @auth
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-6">
                <form method="POST" action="{{ route('community.posts.comments.store', $post->id) }}">
                    @csrf

                    <div class="flex gap-4">
                        <img src="{{ Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : asset('images/dummymountain/rinjani.png') }}"
                            class="w-10 h-10 rounded-full object-cover shrink-0" alt="My Avatar">

                        <div class="grow flex flex-col">
                            <textarea name="body" rows="3" placeholder="Post your reply"
                                class="w-full bg-transparent border-none focus:ring-0 focus:outline-none resize-none text-gray-800 placeholder-gray-400 text-sm p-0 mt-1"
                                required>{{ old('body') }}</textarea>

                            @error('body')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror

                            <div class="flex items-center justify-end border-t border-gray-100 pt-3 mt-2">
                                <button type="submit"
                                    class="bg-[#094174] hover:bg-[#105DA3] text-white font-bold py-1.5 px-5 rounded-full text-sm transition">
                                    Reply
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-white rounded-3xl border border-gray-100 p-6 mb-6 text-center text-sm text-gray-500">
                <a href="{{ route('showLogin') }}" class="text-[#094174] font-bold hover:underline">Log in</a> to reply to this
                post.
            </div>
        @endauth

    </div>
@endsection
