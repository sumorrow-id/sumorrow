<div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-8 mt-6">

    <div class="space-y-6">
        <div class="flex justify-between items-end mb-6">
            <h2 class="text-2xl font-bold text-[#0F172A]">{{ __('profile.forum_posts') }}</h2>
            <a href="{{ route('community.explore') }}"
                class="text-sm font-bold text-[#2A5C9A] hover:underline">{{ __('profile.go_to_forum') }}</a>
        </div>

        @forelse($forumPosts as $post)
            <div
                class="bg-white rounded-3xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)]">

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
                    <p class="text-[#1a2b4c] text-sm md:text-base mb-3">{{ Str::limit($post->body, 300) }}</p>
                @endif

                @if ($post->images->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                        @foreach ($post->images->take(4) as $image)
                            <img src="{{ str_contains($image->image_url, 'http') ? $image->image_url : asset($image->image_url) }}"
                                alt="{{ __('community.post_image_alt') }}"
                                class="w-full aspect-square rounded-xl object-cover">
                        @endforeach
                    </div>
                @endif

                @if ($post->gif_url)
                    <div class="mb-3">
                        <img src="{{ $post->gif_url }}" alt="{{ __('community.gif_alt') }}"
                            class="w-full max-h-72 object-cover rounded-xl border border-gray-100">
                    </div>
                @endif

                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <a href="{{ route('community.posts.show', $post->id) }}"
                        class="text-sm font-bold text-[#2A5C9A] hover:underline">{{ __('profile.view_post') }}</a>

                    <form method="POST" action="{{ route('community.posts.destroy', $post->id) }}"
                        class="confirm-submit-form"
                        data-confirm-title="{{ __('community.delete_post') }}"
                        data-confirm-message="{{ __('community.confirm_delete_post') }}"
                        data-confirm-label="{{ __('common.delete') }}"
                        data-confirm-variant="danger">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-sm font-bold text-red-500 hover:text-red-600 hover:underline transition">
                            {{ __('community.delete_post') }}
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="bg-white rounded-3xl p-6 shadow-sm text-center py-12">
                <p class="text-gray-500 mb-4">{{ __('profile.no_forum_posts') }}</p>
                <a href="{{ route('community.explore') }}"
                    class="inline-block bg-[#094174] hover:bg-[#105DA3] text-white text-sm font-bold py-2 px-6 rounded-full transition shadow-md">
                    {{ __('profile.go_to_forum') }}
                </a>
            </div>
        @endforelse

    </div>

    <div class="lg:col-span-1 h-full">
        <div
            class="bg-white rounded-3xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] h-full flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-[#0F172A] text-lg">{{ __('profile.last_reviews') }}</h3>
                <span
                    class="bg-gray-100 text-gray-500 text-[10px] uppercase font-bold px-2.5 py-1 rounded-md">{{ __('profile.recent') }}</span>
            </div>

            <div class="grow flex flex-col gap-5">
                @forelse ($lastReviews as $review)
                    @continue(! $review->mountain)
                    <div class="flex items-center justify-between group cursor-pointer"
                        onclick="window.location.href='{{ route('explore.show', $review->mountain->id) }}'">
                        <div class="flex items-center gap-3">
                            <img src="{{ $review->mountain->images->first()?->image_url ?? asset('images/default-mountain.jpg') }}"
                                class="w-11 h-11 rounded-xl object-cover group-hover:ring-2 ring-[#094174] transition-all">
                            <div>
                                <h4 class="text-sm font-bold text-[#0F172A] group-hover:text-[#094174] transition">
                                    {{ Str::limit($review->mountain->name, 20) }}</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">
                                    {{ $review->created_at->translatedFormat('M d, Y') }}</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-yellow-500 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-3 w-3 h-3">
                                <path fill-rule="evenodd"
                                    d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ number_format($review->score, 1) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <p class="text-sm text-gray-500">{{ __('profile.no_reviews_yet') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
